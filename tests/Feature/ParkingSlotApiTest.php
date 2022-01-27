<?php


class ParkingSlotApiTest extends TestCase
{
    public function testGetParkingSlotsFeature()
    {
        // 200 //
        $response = $this->get("/v1/api/parking/slots");
        $response->assertResponseStatus(200);
    }

    public function testClearParkingSlotsFeature()
    {
        // 200 //
        $response = $this->delete("/v1/api/parking/slots");
        $response->assertResponseStatus(200);
    }
}
