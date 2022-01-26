<?php


namespace App\Models;


class ParkedCar extends BaseModel
{
    protected $table = 'parked_cars';

    protected $fillable = [
        'parking_slot_id',
        'car_plate',
        'parked_at',
        'unparked_at',
        'is_continuous'
    ];

    public function parkingSlot()
    {
        return $this->belongsTo(ParkingSlot::class, 'parking_slot_id');
    }
}
