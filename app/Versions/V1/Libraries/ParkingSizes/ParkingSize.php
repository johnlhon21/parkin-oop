<?php


namespace App\Versions\V1\Libraries\ParkingSizes;


class ParkingSize
{
    protected $size = null;

    protected $flatRate = 40.00;

    protected $flatRateHours = 3;

    protected $perHourRate = 0.00;

    protected $twentyFourHoursParkingRate = 5000.00;

    /**
     * @return null|string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @param string $size
     */
    public function setSize(string $size): void
    {
        $this->size = $size;
    }

    /**
     * @return float
     */
    public function getFlatRate(): float
    {
        return $this->flatRate;
    }

    /**
     * @param float $flatRate
     */
    public function setFlatRate(float $flatRate): void
    {
        $this->flatRate = $flatRate;
    }

    /**
     * @return int
     */
    public function getFlatRateHours(): int
    {
        return $this->flatRateHours;
    }

    /**
     * @param int $flatRateHours
     */
    public function setFlatRateHours(int $flatRateHours): void
    {
        $this->flatRateHours = $flatRateHours;
    }

    /**
     * @return float
     */
    public function getPerHourRate(): float
    {
        return $this->perHourRate;
    }

    /**
     * @param float $perHourRate
     */
    public function setPerHourRate(float $perHourRate): void
    {
        $this->perHourRate = $perHourRate;
    }

    /**
     * @return float
     */
    public function getTwentyFourHoursParkingRate(): float
    {
        return $this->twentyFourHoursParkingRate;
    }

    /**
     * @param float $twentyFourHoursParkingRate
     */
    public function setTwentyFourHoursParkingRate(float $twentyFourHoursParkingRate): void
    {
        $this->twentyFourHoursParkingRate = $twentyFourHoursParkingRate;
    }


}
