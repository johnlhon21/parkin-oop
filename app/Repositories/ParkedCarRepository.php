<?php


namespace App\Repositories;


use App\Abstracts\BaseRepository;
use App\Models\ParkedCar;

class ParkedCarRepository extends BaseRepository
{
    public function __construct(ParkedCar $model)
    {
        parent::__construct($model);
    }

    public function getParkedCarsWithinAnHour($carPlate)
    {
        $query = $this->model
            ->where('car_plate', $carPlate)
            ->whereRaw("parked_at > DATE_SUB(NOW(), INTERVAL '1' HOUR) ")
            ->orderBy('parked_at', 'DESC')
            ->limit(1)
            ->first();

        return $query;
    }

    public function findParkedCarById($id)
    {
        return $this->model->with('parkingSlot')->where('id', $id)->first();
    }
}
