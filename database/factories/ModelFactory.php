<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(\App\Models\ParkingSlot::class, function (Faker\Generator $faker) {
    return [
        'uuid' => $faker->uuid,
        'name' => $faker->name,
        'distance' => 3,
        'size' => 'small',
        'is_available' => true,
        'nearest_entry_point' => 'Entry Point 1',
        'entry_point_coverage' => 3,
    ];
});


$factory->define(\App\Models\ParkedCar::class, function (Faker\Generator $faker) {
    return [
        'parking_slot_id' => 1,
        'car_plate' => $faker->randomLetter,
        'parked_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
        'unparked_at' => null,
        'is_continuous' => 0
    ];
});
