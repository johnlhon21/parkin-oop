<?php


class ParkingApiTest extends TestCase
{
    public function testParkingFeature()
    {
        // 422 //
        $response = $this->post("/v1/api/park/3", []);
        $response->assertResponseStatus(422);

        // 500 //
        $response = $this->post("/v1/api/park/3", [
            'car_plate' => 'LOT-110',
            'car_size' => 'small'
        ]);

        // 200 //

        // Create Test Data
        $slot = factory(\App\Models\ParkingSlot::class)->create();

        $response = $this->post("/v1/api/park/" . $slot->entry_point_coverage, [
            'car_plate' => $this->faker->randomLetter,
            'car_size' => 'small'
        ]);

        $response->assertResponseStatus(200);
        $response->seeJsonStructure([
            "status",
            "message",
            "data"
        ]);

    }

    public function testUnparkingFeature()
    {
        // 404 //
        $response = $this->post("/v1/api/unpark/0", []);
        $response->assertResponseStatus(404);

        // 500 //
        // Create Test Data
        $slot = factory(\App\Models\ParkingSlot::class)->create();
        $slot->size = 'invalid_parking_size';
        $slot->save();

        $parkedCar = factory(\App\Models\ParkedCar::class)->create();
        $parkedCar->parking_slot_id =  $slot->id;
        $parkedCar->save();

        $response = $this->post("/v1/api/unpark/" . $parkedCar->id);
        $response->assertResponseStatus(500);

        // 200 //
        // Create Test Data
        $slot = factory(\App\Models\ParkingSlot::class)->create();
        $parkedCar = factory(\App\Models\ParkedCar::class)->create();
        $parkedCar->parking_slot_id =  $slot->id;
        $parkedCar->save();

        $response = $this->post("/v1/api/unpark/" . $parkedCar->id);

        $response->assertResponseStatus(200);
        $response->seeJsonStructure([
            "status",
            "message",
            "data"
        ]);

    }

}
