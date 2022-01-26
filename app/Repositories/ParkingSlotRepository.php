<?php


namespace App\Repositories;


use App\Abstracts\BaseRepository;
use App\Models\ParkingSlot;
use App\Versions\V1\Exceptions\InvalidEntryPointException;
use App\Versions\V1\Exceptions\NoParkingSlotException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

class ParkingSlotRepository extends BaseRepository
{
    public function __construct(ParkingSlot $model)
    {
        parent::__construct($model);
    }

    public function getParkingSlots()
    {
        return $this->model->get();
    }

    public function truncate()
    {
        $this->model->truncate();
        return true;
    }

    public function isValidEntryPointCoverage($entryPointCoverage)
    {
        return $this->model
            ->where('entry_point_coverage', $entryPointCoverage)
            ->count() > 0;
    }

    public function getNearestParkingSlot($size, $entryPointCoverage)
    {
        if (! $this->isValidEntryPointCoverage($entryPointCoverage)) {
            throw new InvalidEntryPointException();
        }

        $query = $this->model
            ->select([
                '*',
                DB::raw("ABS(distance - $entryPointCoverage) as nearest")
            ])
            ->where('size', strtolower($size))
            ->where('is_available', true)
            ->orderBy('nearest', 'ASC')
            ->limit(1);

        if ($query->count() < 1) {
           throw new NoParkingSlotException();
        }

        return $query->first();
    }
}
