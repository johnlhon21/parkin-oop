<?php


class ParkingSlotServiceTest extends TestCase
{
    protected $parkingSlotRepository;

    protected $parkingSlot;

    protected $request;

    protected $parkingSlotService;

    public function setUp(): void
    {
        parent::setUp();

        $this->parkingSlotRepository = \Mockery::mock(app(\App\Repositories\ParkingSlotRepository::class));
        $this->request = \Mockery::mock(app(\Illuminate\Http\Request::class));
        $this->parkingSlot = \Mockery::mock(app(\App\Versions\V1\Libraries\ParkingSlot::class));

        $this->parkingSlotService = new \App\Versions\V1\Services\ParkingSlotService($this->request, $this->parkingSlot, $this->parkingSlotRepository);
    }

    public function testInitializedWithInvalidConfigurationMustThrowException()
    {
        $parkingSLotContext = \Mockery::mock(app(\App\Models\ParkingSlot::class));
        $parkingSLotContext->makePartial()->shouldReceive('count')->andReturn(0);
        $this->parkingSlotRepository->makePartial()->shouldReceive('getParkingSlots')->andReturn($parkingSLotContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('insert')->andReturn(false);
        $this->parkingSlot->makePartial()->shouldReceive('generate')->andThrow(new Exception('Invalid parking slot per set.'));
        $this->parkingSlot->makePartial()->shouldReceive('getParkingSlots')->andReturn([]);

        $result = $this->parkingSlotService->initialize();

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('parking_slots', $result->data);
        $this->assertEmpty($result->data['parking_slots']);
        $this->assertEquals(500, $result->status);
        $this->assertEquals('Invalid parking slot per set.', $result->message);
    }

    public function testInitializedAndGenerateParkingSlots()
    {
        $parkingSLotContext = \Mockery::mock(app(\App\Models\ParkingSlot::class));
        $parkingSLotContext->makePartial()->shouldReceive('count')->andReturn(0);
        $this->parkingSlotRepository->makePartial()->shouldReceive('getParkingSlots')->andReturn($parkingSLotContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('insert')->andReturn(true);
        $this->parkingSlot->makePartial()->shouldReceive('generate')->andReturn(true);
        $this->parkingSlot->makePartial()->shouldReceive('getParkingSlots')->andReturn(['name' => 'Parking Slot 1']);

        $result = $this->parkingSlotService->initialize();

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('parking_slots', $result->data);
        $this->assertNotEmpty($result->data['parking_slots']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Parking Slots Successfully Generated', $result->message);
    }

    public function testInitializedAndRetrievedGeneratedParkingSlots()
    {
        $parkingSLotContext = \Mockery::mock(app(\App\Models\ParkingSlot::class));
        $parkingSLotContext->makePartial()->shouldReceive('count')->andReturn(1);
        $this->parkingSlotRepository->makePartial()->shouldReceive('getParkingSlots')->andReturn($parkingSLotContext);

        $result = $this->parkingSlotService->initialize();

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('parking_slots', $result->data);
        $this->assertNotEmpty($result->data['parking_slots']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Parking Slots Successfully Retrieved', $result->message);
    }

    public function testTruncateParkingSlots()
    {
        $this->parkingSlotRepository->makePartial()->shouldReceive('truncate')->andReturn(true);

        $result = $this->parkingSlotService->truncate();

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('parking_slots', $result->data);
        $this->assertEmpty($result->data['parking_slots']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Parking Slots Successfully Cleared', $result->message);
    }
}
