<?php


namespace App\Models;


class ParkingSlot extends BaseModel
{
    protected $table = 'parking_slots';

    protected $fillable = [
        'uuid',
        'name',
        'distance',
        'size',
        'is_available',
        'nearest_entry_point',
        'entry_point_coverage'
    ];

    protected $appends = [
      'parked_car'
    ];

    public function getParkedCarAttribute()
    {
        if ($this->attributes['is_available']) {
            return null;
        }

        $parkedCar = ParkedCar::where('parking_slot_id', $this->attributes['id'])
            ->whereNull('unparked_at')->first();

        return $parkedCar;
    }
}
