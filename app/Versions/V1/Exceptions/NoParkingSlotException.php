<?php


namespace App\Versions\V1\Exceptions;


class NoParkingSlotException extends \Exception
{
    protected $message = 'No Parking Slot Available';
}
