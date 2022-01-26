<?php


namespace App\Versions\V1\Libraries\ParkingSizes;


class ParkingSizeFactory
{
    public static function make($size): ParkingSize
    {
        switch ($size) {
            case 'small':
                return new SmallParking();
                break;
            case 'medium':
                return new MediumParking();
                break;
            case 'large' :
                return new LargeParking();
            default:
                throw new \InvalidArgumentException("Invalid parking size");
        }
    }
}
