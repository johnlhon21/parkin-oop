<?php


namespace App\Repositories;


use App\Abstracts\BaseRepository;
use App\Models\ParkingSlot;
use App\Versions\V1\Exceptions\InvalidEntryPointException;
use App\Versions\V1\Exceptions\NoParkingSlotException;
use Illuminate\Support\Facades\DB;

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

        $epcOrder = $size == 'large' ? 'ASC' : 'DESC';

        $query = $this->model
            ->select([
                '*',
                DB::raw("ABS(distance - $entryPointCoverage) as nearest")
            ])
            ->where('size', strtolower($size))
            ->where('is_available', true)
            ->orderBy('nearest', 'ASC')
            ->orderBy('entry_point_coverage', $epcOrder);
//            ->limit(1);
        if ($query->count() < 1) {
            throw new NoParkingSlotException();
        }

//        return $query->first();
        $collection = $query->get()->groupBy('entry_point_coverage');
        $slot = null;
        foreach ($collection as $key =>  $item) {

           if ($key == $entryPointCoverage) {
                $slot = $item->last();
                break;
           }

            if ($key < $entryPointCoverage) {
                $slot = $item->last();
                break;
            }

           if ($key > $entryPointCoverage) {
               $slot = $item->first();
               break;
           }
        }
        return $slot;
    }
}
