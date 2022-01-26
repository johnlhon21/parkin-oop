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
        'is_available'
    ];
}
