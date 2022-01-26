<?php


use Illuminate\Http\Request;
use App\Repositories\ParkingSlotRepository;
use App\Repositories\ParkedCarRepository;
use App\Versions\V1\Services\ParkingService;

class ParkingServiceTest extends TestCase
{
    protected $parkingSlotRepository;

    protected $parkedCarRepository;

    protected $request;

    protected $parkingService;

    public function setUp(): void
    {
        parent::setUp();

        $this->parkingSlotRepository = \Mockery::mock(app(ParkingSlotRepository::class));
        $this->request = \Mockery::mock(app(Request::class));
        $this->parkedCarRepository = \Mockery::mock(app(ParkedCarRepository::class));

        $this->parkingService = new ParkingService($this->request, $this->parkingSlotRepository, $this->parkedCarRepository);
    }

    public function testParkWithInvalidEntryPointMustThrowException()
    {
        $this->parkingSlotRepository->makePartial()->shouldReceive('getNearestParkingSlot')
            ->andThrow(new \App\Versions\V1\Exceptions\InvalidEntryPointException());

        $result = $this->parkingService->park('large', 3);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('parking_details', $result->data);
        $this->assertArrayHasKey('parked', $result->data);
        $this->assertNull($result->data['parking_details']);
        $this->assertFalse($result->data['parked']);
        $this->assertEquals(500, $result->status);
        $this->assertEquals('Invalid Entry Point', $result->message);
    }

    public function testParkWithNoParkingSlotAvailable()
    {
        $this->parkingSlotRepository->makePartial()->shouldReceive('getNearestParkingSlot')
            ->andThrow(new \App\Versions\V1\Exceptions\NoParkingSlotException());

        $result = $this->parkingService->park('large', 3);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('parking_details', $result->data);
        $this->assertArrayHasKey('parked', $result->data);
        $this->assertNull($result->data['parking_details']);
        $this->assertFalse($result->data['parked']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Full Parking', $result->message);
    }

    public function testParkWithAvailableParkingSlot()
    {
        $parkingSlotContext = new \App\Models\ParkingSlot();
        $this->parkingSlotRepository->makePartial()->shouldReceive('getNearestParkingSlot')->andReturn($parkingSlotContext);
        $this->parkingSlotRepository->shouldReceive('update')->andReturn($parkingSlotContext);
        $this->parkedCarRepository->shouldReceive('getParkedCarsWithinAnHour')->andReturn(new \App\Models\ParkedCar());

        $result = $this->parkingService->park('small', 3);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('parking_details', $result->data);
        $this->assertArrayHasKey('parked', $result->data);
        $this->assertNotNull($result->data['parking_details']);
        $this->assertTrue($result->data['parked']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Successfully parked', $result->message);
    }

    public function testUnparkWithParkedCarNotFound() {
        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn(null);

        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNull($result->data['parking_fee_details']);
        $this->assertFalse($result->data['unparked']);
        $this->assertEquals(404, $result->status);
        $this->assertEquals('Parked Car Not Found', $result->message);
    }

    public function testUnparkWithAlreadyUnparkedCar() {
        $parkedCarContext = new \App\Models\ParkedCar();
        $parkedCarContext->parked_at = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $parkedCarContext->unparked_at = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn($parkedCarContext);
        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNull($result->data['parking_fee_details']);
        $this->assertFalse($result->data['unparked']);
        $this->assertEquals(404, $result->status);
        $this->assertEquals('Parked Car is already outside of the complex', $result->message);
    }

    public function testUnparkAndWithInvalidParkingSizeMustThrowException() {
        $parkingSlotContext = new \App\Models\ParkingSlot();
        $parkingSlotContext->size = 'xxx';
        $parkedCarContext = new \App\Models\ParkedCar();
        $parkedCarContext->parked_at = \Carbon\Carbon::now()->subDay(2)->format('Y-m-d H:i:s');
        $parkedCarContext->unparked_at = null;
        $parkedCarContext->parkingSlot = $parkingSlotContext;

        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn($parkedCarContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('update')->andReturn(false);
        $this->parkedCarRepository->makePartial()->shouldReceive('update')->andReturn(false);

        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNull($result->data['parking_fee_details']);
        $this->assertFalse($result->data['unparked']);
        $this->assertEquals(500, $result->status);
        $this->assertEquals('Invalid parking size', $result->message);
    }

    public function testUnparkSuccessWithSmallParkingSlotWithinFlatRateHours() {
        $parkingSlotContext = new \App\Models\ParkingSlot();
        $parkingSlotContext->size = 'small';
        $parkedCarContext = new \App\Models\ParkedCar();
        $parkedCarContext->parked_at = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $parkedCarContext->unparked_at = null;
        $parkedCarContext->parkingSlot = $parkingSlotContext;

        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn($parkedCarContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('update')->andReturn(true);
        $this->parkedCarRepository->makePartial()->shouldReceive('update')->andReturn(true);

        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNotNull($result->data['parking_fee_details']);
        $this->assertEquals('small', $result->data['parking_fee_details']['parking_size']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['flat_rate']);
        $this->assertEquals('20.00', $result->data['parking_fee_details']['hourly_rate']);
        $this->assertGreaterThanOrEqual(0, $result->data['parking_fee_details']['total_hours']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['total_parking_fee']);
        $this->assertTrue($result->data['unparked']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Unparked Success', $result->message);
    }

    public function testUnparkSuccessWithMediumParkingSlotWithinFlatRateHours() {
        $parkingSlotContext = new \App\Models\ParkingSlot();
        $parkingSlotContext->size = 'medium';
        $parkedCarContext = new \App\Models\ParkedCar();
        $parkedCarContext->parked_at = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $parkedCarContext->unparked_at = null;
        $parkedCarContext->parkingSlot = $parkingSlotContext;

        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn($parkedCarContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('update')->andReturn(true);
        $this->parkedCarRepository->makePartial()->shouldReceive('update')->andReturn(true);

        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNotNull($result->data['parking_fee_details']);
        $this->assertEquals('medium', $result->data['parking_fee_details']['parking_size']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['flat_rate']);
        $this->assertEquals('60.00', $result->data['parking_fee_details']['hourly_rate']);
        $this->assertGreaterThanOrEqual(0, $result->data['parking_fee_details']['total_hours']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['total_parking_fee']);
        $this->assertTrue($result->data['unparked']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Unparked Success', $result->message);
    }

    public function testUnparkSuccessWithLargeParkingSlotWithinFlatRateHours() {
        $parkingSlotContext = new \App\Models\ParkingSlot();
        $parkingSlotContext->size = 'large';
        $parkedCarContext = new \App\Models\ParkedCar();
        $parkedCarContext->parked_at = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $parkedCarContext->unparked_at = null;
        $parkedCarContext->parkingSlot = $parkingSlotContext;

        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn($parkedCarContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('update')->andReturn(true);
        $this->parkedCarRepository->makePartial()->shouldReceive('update')->andReturn(true);

        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNotNull($result->data['parking_fee_details']);
        $this->assertEquals('large', $result->data['parking_fee_details']['parking_size']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['flat_rate']);
        $this->assertEquals('100.00', $result->data['parking_fee_details']['hourly_rate']);
        $this->assertGreaterThanOrEqual(0, $result->data['parking_fee_details']['total_hours']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['total_parking_fee']);
        $this->assertTrue($result->data['unparked']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Unparked Success', $result->message);
    }

    public function testUnparkSuccessWithSmallParkingSlotAndWithAdditionalHours() {
        $parkingSlotContext = new \App\Models\ParkingSlot();
        $parkingSlotContext->size = 'small';
        $parkedCarContext = new \App\Models\ParkedCar();
        $parkedCarContext->parked_at = \Carbon\Carbon::now()->subHour(4)->format('Y-m-d H:i:s');
        $parkedCarContext->unparked_at = null;
        $parkedCarContext->parkingSlot = $parkingSlotContext;

        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn($parkedCarContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('update')->andReturn(true);
        $this->parkedCarRepository->makePartial()->shouldReceive('update')->andReturn(true);

        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNotNull($result->data['parking_fee_details']);
        $this->assertEquals('small', $result->data['parking_fee_details']['parking_size']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['flat_rate']);
        $this->assertEquals('20.00', $result->data['parking_fee_details']['hourly_rate']);
        $this->assertEquals(4, $result->data['parking_fee_details']['total_hours']);
        $this->assertEquals('60.00', $result->data['parking_fee_details']['total_parking_fee']);
        $this->assertTrue($result->data['unparked']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Unparked Success', $result->message);
    }

    public function testUnparkSuccessWithMediumParkingSlotAndWithAdditionalHours() {
        $parkingSlotContext = new \App\Models\ParkingSlot();
        $parkingSlotContext->size = 'medium';
        $parkedCarContext = new \App\Models\ParkedCar();
        $parkedCarContext->parked_at = \Carbon\Carbon::now()->subHour(4)->format('Y-m-d H:i:s');
        $parkedCarContext->unparked_at = null;
        $parkedCarContext->parkingSlot = $parkingSlotContext;

        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn($parkedCarContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('update')->andReturn(true);
        $this->parkedCarRepository->makePartial()->shouldReceive('update')->andReturn(true);

        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNotNull($result->data['parking_fee_details']);
        $this->assertEquals('medium', $result->data['parking_fee_details']['parking_size']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['flat_rate']);
        $this->assertEquals('60.00', $result->data['parking_fee_details']['hourly_rate']);
        $this->assertEquals(4, $result->data['parking_fee_details']['total_hours']);
        $this->assertEquals('100.00', $result->data['parking_fee_details']['total_parking_fee']);
        $this->assertTrue($result->data['unparked']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Unparked Success', $result->message);
    }

    public function testUnparkSuccessWithLargeParkingSlotAndWithAdditionalHours() {
        $parkingSlotContext = new \App\Models\ParkingSlot();
        $parkingSlotContext->size = 'large';
        $parkedCarContext = new \App\Models\ParkedCar();
        $parkedCarContext->parked_at = \Carbon\Carbon::now()->subHour(4)->format('Y-m-d H:i:s');
        $parkedCarContext->unparked_at = null;
        $parkedCarContext->parkingSlot = $parkingSlotContext;

        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn($parkedCarContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('update')->andReturn(true);
        $this->parkedCarRepository->makePartial()->shouldReceive('update')->andReturn(true);

        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNotNull($result->data['parking_fee_details']);
        $this->assertEquals('large', $result->data['parking_fee_details']['parking_size']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['flat_rate']);
        $this->assertEquals('100.00', $result->data['parking_fee_details']['hourly_rate']);
        $this->assertEquals(4, $result->data['parking_fee_details']['total_hours']);
        $this->assertEquals('140.00', $result->data['parking_fee_details']['total_parking_fee']);
        $this->assertTrue($result->data['unparked']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Unparked Success', $result->message);
    }

    public function testUnparkSuccessWithSmallParkingSlotWithAdditionalHoursAndContinuousRate() {
        $parkingSlotContext = new \App\Models\ParkingSlot();
        $parkingSlotContext->size = 'small';
        $parkedCarContext = new \App\Models\ParkedCar();
        $parkedCarContext->parked_at = \Carbon\Carbon::now()->addHour(4)->format('Y-m-d H:i:s');
        $parkedCarContext->unparked_at = null;
        $parkedCarContext->parkingSlot = $parkingSlotContext;
        $parkedCarContext->is_continuous = true;

        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn($parkedCarContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('update')->andReturn(true);
        $this->parkedCarRepository->makePartial()->shouldReceive('update')->andReturn(true);

        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNotNull($result->data['parking_fee_details']);
        $this->assertEquals('small', $result->data['parking_fee_details']['parking_size']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['flat_rate']);
        $this->assertEquals('20.00', $result->data['parking_fee_details']['hourly_rate']);
        $this->assertEquals(4, $result->data['parking_fee_details']['total_hours']);
        $this->assertEquals('20.00', $result->data['parking_fee_details']['total_parking_fee']);
        $this->assertTrue($result->data['unparked']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Unparked Success', $result->message);
    }

    public function testUnparkSuccessWithMediumParkingSlotWithAdditionalHoursAndContinuousRate() {
        $parkingSlotContext = new \App\Models\ParkingSlot();
        $parkingSlotContext->size = 'medium';
        $parkedCarContext = new \App\Models\ParkedCar();
        $parkedCarContext->parked_at = \Carbon\Carbon::now()->addHour(4)->format('Y-m-d H:i:s');
        $parkedCarContext->unparked_at = null;
        $parkedCarContext->parkingSlot = $parkingSlotContext;
        $parkedCarContext->is_continuous = true;

        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn($parkedCarContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('update')->andReturn(true);
        $this->parkedCarRepository->makePartial()->shouldReceive('update')->andReturn(true);

        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNotNull($result->data['parking_fee_details']);
        $this->assertEquals('medium', $result->data['parking_fee_details']['parking_size']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['flat_rate']);
        $this->assertEquals('60.00', $result->data['parking_fee_details']['hourly_rate']);
        $this->assertEquals(4, $result->data['parking_fee_details']['total_hours']);
        $this->assertEquals('60.00', $result->data['parking_fee_details']['total_parking_fee']);
        $this->assertTrue($result->data['unparked']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Unparked Success', $result->message);
    }

    public function testUnparkSuccessWithLargeParkingSlotWithAdditionalHoursAndContinuousRate() {
        $parkingSlotContext = new \App\Models\ParkingSlot();
        $parkingSlotContext->size = 'large';
        $parkedCarContext = new \App\Models\ParkedCar();
        $parkedCarContext->parked_at = \Carbon\Carbon::now()->addHour(4)->format('Y-m-d H:i:s');
        $parkedCarContext->unparked_at = null;
        $parkedCarContext->parkingSlot = $parkingSlotContext;
        $parkedCarContext->is_continuous = true;

        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn($parkedCarContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('update')->andReturn(true);
        $this->parkedCarRepository->makePartial()->shouldReceive('update')->andReturn(true);

        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNotNull($result->data['parking_fee_details']);
        $this->assertEquals('large', $result->data['parking_fee_details']['parking_size']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['flat_rate']);
        $this->assertEquals('100.00', $result->data['parking_fee_details']['hourly_rate']);
        $this->assertEquals(4, $result->data['parking_fee_details']['total_hours']);
        $this->assertEquals('100.00', $result->data['parking_fee_details']['total_parking_fee']);
        $this->assertTrue($result->data['unparked']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Unparked Success', $result->message);
    }

    public function testUnparkSuccessWithSmallParkingSlotAndWithExceedingTwentyFourHours() {
        $parkingSlotContext = new \App\Models\ParkingSlot();
        $parkingSlotContext->size = 'small';
        $parkedCarContext = new \App\Models\ParkedCar();
        $parkedCarContext->parked_at = \Carbon\Carbon::now()->subDay(2)->format('Y-m-d H:i:s');
        $parkedCarContext->unparked_at = null;
        $parkedCarContext->parkingSlot = $parkingSlotContext;

        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn($parkedCarContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('update')->andReturn(true);
        $this->parkedCarRepository->makePartial()->shouldReceive('update')->andReturn(true);

        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNotNull($result->data['parking_fee_details']);
        $this->assertEquals('small', $result->data['parking_fee_details']['parking_size']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['flat_rate']);
        $this->assertEquals('20.00', $result->data['parking_fee_details']['hourly_rate']);
        $this->assertEquals(48, $result->data['parking_fee_details']['total_hours']);
        $this->assertEquals('10,000.00', $result->data['parking_fee_details']['total_parking_fee']);
        $this->assertTrue($result->data['unparked']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Unparked Success', $result->message);
    }

    public function testUnparkSuccessWithMediumParkingSlotAndWithExceedingTwentyFourHours() {
        $parkingSlotContext = new \App\Models\ParkingSlot();
        $parkingSlotContext->size = 'medium';
        $parkedCarContext = new \App\Models\ParkedCar();
        $parkedCarContext->parked_at = \Carbon\Carbon::now()->subDay(2)->format('Y-m-d H:i:s');
        $parkedCarContext->unparked_at = null;
        $parkedCarContext->parkingSlot = $parkingSlotContext;

        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn($parkedCarContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('update')->andReturn(true);
        $this->parkedCarRepository->makePartial()->shouldReceive('update')->andReturn(true);

        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNotNull($result->data['parking_fee_details']);
        $this->assertEquals('medium', $result->data['parking_fee_details']['parking_size']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['flat_rate']);
        $this->assertEquals('60.00', $result->data['parking_fee_details']['hourly_rate']);
        $this->assertEquals(48, $result->data['parking_fee_details']['total_hours']);
        $this->assertEquals('10,000.00', $result->data['parking_fee_details']['total_parking_fee']);
        $this->assertTrue($result->data['unparked']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Unparked Success', $result->message);
    }

    public function testUnparkSuccessWithLargeParkingSlotAndWithExceedingTwentyFourHours() {
        $parkingSlotContext = new \App\Models\ParkingSlot();
        $parkingSlotContext->size = 'large';
        $parkedCarContext = new \App\Models\ParkedCar();
        $parkedCarContext->parked_at = \Carbon\Carbon::now()->subDay(2)->format('Y-m-d H:i:s');
        $parkedCarContext->unparked_at = null;
        $parkedCarContext->parkingSlot = $parkingSlotContext;

        $this->parkedCarRepository->makePartial()->shouldReceive('findParkedCarById')->andReturn($parkedCarContext);
        $this->parkingSlotRepository->makePartial()->shouldReceive('update')->andReturn(true);
        $this->parkedCarRepository->makePartial()->shouldReceive('update')->andReturn(true);

        $result = $this->parkingService->unpark(1);

        $this->assertNotEmpty($result->data);
        $this->assertArrayHasKey('unparked', $result->data);
        $this->assertArrayHasKey('parking_fee_details', $result->data);
        $this->assertNotNull($result->data['parking_fee_details']);
        $this->assertEquals('large', $result->data['parking_fee_details']['parking_size']);
        $this->assertEquals('40.00', $result->data['parking_fee_details']['flat_rate']);
        $this->assertEquals('100.00', $result->data['parking_fee_details']['hourly_rate']);
        $this->assertEquals(48, $result->data['parking_fee_details']['total_hours']);
        $this->assertEquals('10,000.00', $result->data['parking_fee_details']['total_parking_fee']);
        $this->assertTrue($result->data['unparked']);
        $this->assertEquals(200, $result->status);
        $this->assertEquals('Unparked Success', $result->message);
    }

}
