<?php


namespace App\Versions\V1\Http\Controllers\Api\Parking;


use App\Http\Controllers\Controller;
use App\Versions\V1\Services\ParkingSlotServiceInterface;

class ParkingSlotController extends Controller
{
    protected $parkingSlotService;

    public function __construct(ParkingSlotServiceInterface $parkingSlotService)
    {
        $this->parkingSlotService = $parkingSlotService;
    }

    public function get()
    {
        $response = $this->parkingSlotService->initialize();

        return response()->json($response, $response->status);
    }

    public function truncate()
    {
        $response = $this->parkingSlotService->truncate();
        return response()->json($response, $response->status);
    }
}
