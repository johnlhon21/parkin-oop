<?php


namespace App\Versions\V1\Services;


use App\Abstracts\BaseService;
use App\Repositories\ParkingSlotRepository;
use App\Versions\V1\Libraries\ParkingSlot;
use Illuminate\Http\Request;

class ParkingSlotService extends BaseService implements ParkingSlotServiceInterface
{
    protected $parkingSlots;

    protected $parkingSlotRepository;

    public function __construct(Request $request, ParkingSlot $parkingSlot, ParkingSlotRepository $parkingSlotRepository)
    {
        parent::__construct($request);
        $this->parkingSlots = $parkingSlot;
        $this->parkingSlotRepository = $parkingSlotRepository;
    }

    public function initialize()
    {
        try {
            // Return Parking Slots
            $slots = $this->parkingSlotRepository->getParkingSlots();

            if ($slots->count() > 0) {
                return $this->response()->with([
                    'parking_slots' => $slots,
                ], 'Parking Slots Successfully Retrieved', 200);
            }

            // Generate Parking Slots
            $this->parkingSlots->generate();
            $this->parkingSlotRepository->insert($this->parkingSlots->getParkingSlots());
            return $this->response()->with([
                'parking_slots' => $this->parkingSlots->getParkingSlots(),
            ], 'Parking Slots Successfully Generated', 200);

        } catch (\Exception $exception) {
            return $this->response()->with([
                'parking_slots' => null,
            ], $exception->getMessage(), 500);
        }
    }

    public function truncate()
    {
        $this->parkingSlotRepository->truncate();
        return $this->response()->with([
            'parking_slots' => [],
        ], 'Parking Slots Successfully Cleared', 200);
    }
}
