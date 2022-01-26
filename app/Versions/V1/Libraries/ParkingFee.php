<?php


namespace App\Versions\V1\Libraries;


use App\Versions\V1\Libraries\ParkingSizes\ParkingSizeFactory;
use Carbon\Carbon;

class ParkingFee
{
    protected $size;

    protected $parkingStartTime;

    protected $continuousRate;

    protected $totalHours = 0;

    protected $parkingFee = 0.00;

    protected $hourlyRate = 0.00;

    protected $flatRate = 0.00;

    public function __construct($size, $parkingStartTime, $continuousRate)
    {
        $this->size = $size;
        $this->parkingStartTime = $parkingStartTime;
        $this->continuousRate = $continuousRate;
    }

    public function calculate(): void
    {
        try {
            // Initialize Variables

            $parkingSize = ParkingSizeFactory::make($this->size);
            $totalHours = $this->calculateTotalHours();
            $this->totalHours = $totalHours;
            $this->flatRate = $parkingSize->getFlatRate();
            $this->hourlyRate = $parkingSize->getPerHourRate();
            $firstParkingPayment = $this->continuousRate ? $parkingSize->getFlatRate() : 0;

            // Flat Rate
            if ($totalHours <= $parkingSize->getFlatRateHours()) {
                $parkingFee =  $parkingSize->getFlatRate();
                $this->parkingFee = $parkingFee - $firstParkingPayment;
            }

            // With Additional Hours
            elseif ($totalHours <= 24) {
               $additionalHours = $totalHours - $parkingSize->getFlatRateHours();
               $additionalHoursFee = $additionalHours * $parkingSize->getPerHourRate();
               $parkingFee = $parkingSize->getFlatRate() + $additionalHoursFee;
               $this->parkingFee = $parkingFee - $firstParkingPayment;
            }

            else {

                // Exceeding 24 hours Parking
                $twentyFourHoursParking = 0;
                $hours = $totalHours;
                while ($hours >= 24) {
                    $twentyFourHoursParking += 1;
                    $hours -= 24;
                }

                $additionalHours = $totalHours - ($twentyFourHoursParking * 24);
                $twentyFourHoursParkingFee = $twentyFourHoursParking * $parkingSize->getTwentyFourHoursParkingRate();
                $additionalHoursFee = $additionalHours * $parkingSize->getPerHourRate();
                $parkingFee = $twentyFourHoursParkingFee + $additionalHoursFee;

                $this->parkingFee = $parkingFee - $firstParkingPayment;
            }

        } catch (\InvalidArgumentException $exception) {
            throw $exception;
        }

    }

    private function calculateTotalHours()
    {
        $diffInMinutes = Carbon::now()->diffInMinutes(Carbon::parse($this->parkingStartTime));
        $totalHours = ceil($diffInMinutes / 60 );
        return $totalHours;
    }

    /**
     * @return int
     */
    public function getTotalHours(): int
    {
        return $this->totalHours;
    }

    /**
     * @return float
     */
    public function getParkingFee(): float
    {
        return $this->parkingFee;
    }

    /**
     * @return float
     */
    public function getHourlyRate(): float
    {
        return $this->hourlyRate;
    }

    /**
     * @return float
     */
    public function getFlatRate(): float
    {
        return $this->flatRate;
    }
}
