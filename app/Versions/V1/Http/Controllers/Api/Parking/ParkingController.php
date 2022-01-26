<?php


namespace App\Versions\V1\Http\Controllers\Api\Parking;


use App\Http\Controllers\Controller;
use App\Versions\V1\Services\ParkingServiceInterface;
use Illuminate\Http\Request;

class ParkingController extends Controller
{
    protected $parkingService;

    public function __construct(ParkingServiceInterface $parkingService)
    {
        $this->parkingService = $parkingService;
    }

    public function park($entryPointCoverage, Request $request)
    {
        $this->validate($request, [
           'car_plate' => 'required',
           'car_size' => 'required|in:small,medium,large',
        ]);

        $response = $this->parkingService->park($request->input('car_size'), $entryPointCoverage);

        return response()->json($response, $response->status);
    }

    public function unpark($parkedCarId)
    {
        $response = $this->parkingService->unpark($parkedCarId);

        return response()->json($response, $response->status);
    }
}
