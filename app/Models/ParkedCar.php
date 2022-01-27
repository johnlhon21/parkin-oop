<?php


namespace App\Models;


use Carbon\Carbon;

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

    protected $appends = ['parked_at_diff'];

    public function parkingSlot()
    {
        return $this->belongsTo(ParkingSlot::class, 'parking_slot_id');
    }

    public function getParkedAtDiffAttribute()
    {
        return $this->attributes['parked_at'] !== null ? Carbon::parse($this->attributes['parked_at'])->diffForHumans() : null;
    }
}
