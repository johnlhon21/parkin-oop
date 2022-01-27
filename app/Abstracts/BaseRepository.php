<?php

namespace App\Abstracts;


use App\Helpers\PaginationHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    /**
     * @var Builder
     */
    protected $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function instance(): Model {
        return $this->model;
    }

    public function findByField($field, $value) {
        return $this->model->where($field, $value)->first();
    }

    public function findAll($field, $value) {
        return $this->model->where($field, $value)->get();
    }

    public function create(array $data) {
        return $this->model->create($data);
    }

    public function update(Model $context, array $data) {
        $context->update($data);
        return $context;
    }

    public function delete(Model $context) {
        return $context->delete();
    }

    public function forceDelete(Model $context) {
        return $context->forceDelete();
    }

    public function deleteByField($field, $value) {
        return $this->model->where($field, $value)->delete();
    }

    public function whereInField($field, array $value) {
        return $this->model->whereIn($field, $value)->get();
    }

    public function getIndexes() {
        return $this->model->indexes();
    }

    public function updateOrCreate(array $uniques, array $values)
    {
        return $this->model->updateOrCreate($uniques, $values);
    }

    public static function paginate(Builder $query, $perPage, $page = 1)
    {
        $paginationHelper = app()->make(PaginationHelper::class);
        $offset = ($page - 1) * $perPage;
        $count = $query->getQuery()->getCountForPagination();

        // IF EMPTY PAGE //
        if($page == 1 && $count == 0) {
            return $paginationHelper->links();
        }

        $max_page = ceil($count / $perPage);

        if( $page > $max_page ) {
            return $paginationHelper->setStatus(404)->links();
        }

        return $paginationHelper
            ->setList($query->limit($perPage)->offset($offset)->get()->toArray())
            ->setMaxPage((int)$max_page)
            ->setPrevPage($page == 1 ? 0 : $page - 1)
            ->setNextPage($page == $max_page ? 0 : $page + 1)
            ->setFirstPage(1)
            ->setLastPage((int) $max_page)
            ->setCurrentPage($page)
            ->setTotal($count)
            ->setPerPage($perPage)
            ->links();
    }

    public function insert(array $data) {
        return $this->model->insert($data);
    }


    public function truncate()
    {
        $this->model->truncate();
        return true;
    }
}
