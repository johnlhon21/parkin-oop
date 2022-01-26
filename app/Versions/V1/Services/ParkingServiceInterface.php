<?php


namespace App\Versions\V1\Services;


interface ParkingServiceInterface
{
    public function park($carSize, $entryPointCoverage);

    public function unpark($parkedCarId);
}
