<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public static function forceIndex($index_raw)
    {
        $model = new static();
        $model->setTable(DB::raw($model->getTable() . ' ' . $index_raw));
        return $model;
    }
}
