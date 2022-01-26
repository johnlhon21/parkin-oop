<?php


namespace App\Versions\V1\Services;


interface CrudServiceInterface
{
    public function list($page = 1);

    public function create();

    public function get($uuid);

    public function update($uuid);

    public function delete($uuid);

    public function archive($uuid, $status);
}
