<?php


namespace App\Versions\V1\Services;


use App\Abstracts\BaseService;
use App\Models\ParkedCar;
use App\Models\ParkingSlot;
use App\Repositories\ParkedCarRepository;
use App\Repositories\ParkingSlotRepository;
use App\Versions\V1\Exceptions\InvalidEntryPointException;
use App\Versions\V1\Exceptions\NoParkingSlotException;
use App\Versions\V1\Libraries\ParkingFee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ParkingService extends BaseService implements ParkingServiceInterface
{
    protected $parkingSlotRepository;

    protected $parkedCarRepository;

    public function __construct(Request $request, ParkingSlotRepository $parkingSlotRepository, ParkedCarRepository $parkedCarRepository)
    {
        parent::__construct($request);
        $this->parkingSlotRepository = $parkingSlotRepository;
        $this->parkedCarRepository = $parkedCarRepository;
    }

    /**
     * @param $carSize
     * @param $entryPointCoverage
     * @return object
     */
    public function park($carSize, $entryPointCoverage)
    {
        try {

            $parkingSlot = $this->parkingSlotRepository->getNearestParkingSlot($carSize, $entryPointCoverage);
            $parking = $this->doParking($this->request->input('car_plate'), $parkingSlot);

            return $this->response()->with([
                'parking_details' => $parking->load('parkingSlot'),
                'parked' => true
            ], 'Successfully parked', 200);

        } catch (NoParkingSlotException $exception) {
            // Upgrade the parking size and then try again to find available slot
            $upgradedParkingSize = $this->upgradeParkingSize($carSize);

            if ($upgradedParkingSize !== null) {
                // Find another slot here
                return $this->park($upgradedParkingSize, $entryPointCoverage);
            } else {
                return $this->response()->with([
                    'parking_details' => null,
                    'parked' => false
                ], 'Full Parking', 400);
            }

        } catch (InvalidEntryPointException $exception) {

            return $this->response()->with([
                'parking_details' => null,
                'parked' => false
            ], $exception->getMessage(), 500);

        } catch (\Exception $exception) {
            return $this->response()->with([
                'parking_details' => null,
                'parked' => false
            ], $exception->getMessage(), 500);
        }
    }

    public function unpark($parkedCarId)
    {
        $parkedCar = $this->parkedCarRepository->findParkedCarById($parkedCarId);

        if ($parkedCar === null) {
            return $this->response()->with([
                'unparked' => false,
                'parking_fee_details' => null,
            ], 'Parked Car Not Found', 404);
        }

        if ($parkedCar->unparked_at !== null) {
            return $this->response()->with([
                'unparked' => false,
                'parking_fee_details' => null
            ], 'Parked Car is already outside of the complex', 404);
        }

        try {

            // Get Parking Fee
            $parkingFee = new ParkingFee($parkedCar->parkingSlot->size, $parkedCar->parked_at, $parkedCar->is_continuous);
            $parkingFee->calculate();

            // UnParked Car
            $this->parkedCarRepository->update($parkedCar, [
               'unparked_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $this->parkingSlotRepository->update($parkedCar->parkingSlot, [
                'is_available' => 1
            ]);

            // Initialized Computed Variables
            $succeedingHours = $parkingFee->getTotalHours() - $parkingFee->getParkingSize()->getFlatRateHours();
            $succeedingHoursFee = $succeedingHours > 0 ? $succeedingHours * $parkingFee->getHourlyRate() : 0;
            $twentyFourHourParking = $parkingFee->getTwentyFourHoursParking();
            $twentyFourHourParkingFee = 0;

            if ($twentyFourHourParking > 0) {
                $succeedingHours = $parkingFee->getTotalHours() - ($twentyFourHourParking * 24);
                $succeedingHoursFee = $succeedingHours > 0 ? $succeedingHours * $parkingFee->getHourlyRate() : 0;
                $twentyFourHourParkingFee = $twentyFourHourParking * $parkingFee->getParkingSize()->getTwentyFourHoursParkingRate();
            }

            return $this->response()->with([
                'unparked' => true,
                'parking_fee_details' => [
                    'car_plate' => $parkedCar->car_plate,
                    'entry_time' => Carbon::parse($parkedCar->parked_at)->toDayDateTimeString(),
                    'exit_time' => Carbon::parse($parkedCar->unparked_at)->toDayDateTimeString(),
                    'parking_size' => $parkedCar->parkingSlot->size,
                    'flat_rate' => number_format($parkingFee->getFlatRate(), 2),
                    'hourly_rate' => number_format($parkingFee->getHourlyRate(),2),
                    'total_hours' => $parkingFee->getTotalHours(),
                    'total_parking_fee' => number_format($parkingFee->getParkingFee(),2),
                    'previous_payment' => $parkedCar->is_continuous == 1 ? number_format($parkingFee->getFlatRate(),2) : null,
                    'flat_rate_hours' =>  $parkingFee->getParkingSize()->getFlatRateHours(),
                    'succeeding_hours' => $succeedingHours > 0 ? $succeedingHours : 0,
                    'twenty_four_hours_rate' => number_format($parkingFee->getParkingSize()->getTwentyFourHoursParkingRate(), 2),
                    'total_hourly_rate' => number_format($succeedingHoursFee, 2),
                    'twenty_four_hour_parking' => $twentyFourHourParking,
                    'twenty_four_hour_parking_fee' => number_format($twentyFourHourParkingFee, 2),
                ]
            ], 'Unparked Success', 200);

        } catch (\Exception $exception) {

            return $this->response()->with([
                'unparked' => false,
                'parking_fee_details' => null
            ], $exception->getMessage(), 500);
        }
    }

    /**
     * @param $size
     * @return string|null
     */
    private function upgradeParkingSize($size)
    {
        switch ($size) {
            case 'small':
                return 'medium';
                break;
            case 'medium' :
                return 'large';
                break;
            case 'large':
                return null;
                break;
            default:
                return null;
        }
    }

    /**
     * @param $carPlate
     * @param ParkingSlot $parkingSlot
     * @return ParkedCar
     * @throws \Exception
     */
    private function doParking($carPlate, ParkingSlot $parkingSlot): ParkedCar
    {
        $parkedCar = $this->parkedCarRepository->getParkedCarsWithinAnHour(strtoupper($carPlate));
        // Continuous Parking
        if ($parkedCar !== null) {
            if ($parkedCar->unparked_at == null) {
                throw new \Exception("Car already is already inside the parking complex.");
            }
            $this->parkedCarRepository->update($parkedCar, [
                'is_continuous' => true,
                'unparked_at' => null,
                'parking_slot_id' => $parkingSlot->id,
            ]);

        } else {
            // Create New Parking
            $parkedAt = Carbon::now()->format('Y-m-d H:i:s');
            $parkedCar = $this->parkedCarRepository->create([
                'car_plate' => strtoupper($this->request->input('car_plate')),
                'parking_slot_id' => $parkingSlot->id,
                'parked_at' => $parkedAt
            ]);
        }

        $this->parkingSlotRepository->update($parkingSlot, ['is_available' => false]);

        return $parkedCar;
    }
}
