<?php


namespace App\Versions\V1\Libraries;


use Ramsey\Uuid\Uuid;

class ParkingSlot
{
    // Parking Slots Configurations
    const ENTRY_POINT =  4;
    const PARKING_SLOT_PER_ENTRY_POINT = 3;
    const SMALL_PARKING_SLOT_PER_ENTRY_POINT = 1;
    const MEDIUM_PARKING_SLOT_PER_ENTRY_POINT = 1;
    const LARGE_PARKING_SLOT_PER_ENTRY_POINT = 1;

    protected $parkingSlots = [];

    protected $entryPoints = [];

    public function generate()
    {
        $slots = self::SMALL_PARKING_SLOT_PER_ENTRY_POINT + self::MEDIUM_PARKING_SLOT_PER_ENTRY_POINT + self::LARGE_PARKING_SLOT_PER_ENTRY_POINT;

        if ($slots !== self::PARKING_SLOT_PER_ENTRY_POINT) {
            throw new \Exception("Invalid parking slot per set.");
        }

        $entryPoints = collect([]);
        $entryPointCoverage = $slots;
        for ($x = 1; $x <= self::ENTRY_POINT; $x++) {
            $entryPoints->push([
                'uuid' => Uuid::uuid1()->toString(),
                'name' => 'Entry Point ' . $x,
                'coverage' => $entryPointCoverage,
            ]);
            $entryPointCoverage += $slots;
        }

        $parkingSlots = collect([]);
        $slot = 1;
        foreach ($entryPoints as $entryPoint) {
            // Create SMall Parking Slots
            for ($small = 0; $small < self::SMALL_PARKING_SLOT_PER_ENTRY_POINT; $small++) {
                $parkingSlots->push([
                    'uuid' => Uuid::uuid1()->toString(),
                    'name' => '[Small] Parking Slot ' . $slot,
                    'distance' => $entryPoint['coverage'] + ($small/100),
                    'size' => 'small',
                    'nearest_entry_point' => $entryPoint['name'],
                    'entry_point_coverage' => $entryPoint['coverage']
                ]);
                $slot += 1;
            }

            // Create Medium Parking Slots
            for ($small = 0; $small < self::MEDIUM_PARKING_SLOT_PER_ENTRY_POINT; $small++) {
                $parkingSlots->push([
                    'uuid' => Uuid::uuid1()->toString(),
                    'name' => '[Medium] Parking Slot ' . $slot,
                    'distance' => $entryPoint['coverage'] + ($small/100),
                    'size' => 'medium',
                    'nearest_entry_point' => $entryPoint['name'],
                    'entry_point_coverage' => $entryPoint['coverage']
                ]);
                $slot += 1;
            }

            // Create Large Parking Slots
            for ($small = 0; $small < self::LARGE_PARKING_SLOT_PER_ENTRY_POINT; $small++) {
                $parkingSlots->push([
                    'uuid' => Uuid::uuid1()->toString(),
                    'name' => '[Large] Parking Slot ' . $slot,
                    'distance' => $entryPoint['coverage'] + ($small/100),
                    'size' => 'large',
                    'nearest_entry_point' => $entryPoint['name'],
                    'entry_point_coverage' => $entryPoint['coverage']
                ]);
                $slot += 1;
            }

            $this->setParkingSlots($parkingSlots->toArray());
            $this->setPEntryPoints($entryPoints->toArray());
        }
    }

    public function setParkingSlots($slots): ParkingSlot
    {
        $this->parkingSlots = $slots;

        return $this;
    }

    public function getParkingSlots(): array
    {
        return $this->parkingSlots;
    }

    public function setPEntryPoints($entryPoints): ParkingSlot
    {
        $this->entryPoints = $entryPoints;

        return $this;
    }

    public function getPEntryPoints(): array
    {
        return $this->entryPoints;
    }
}
