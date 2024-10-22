<?php

namespace App\Repositories;

interface BaseRepositoryInterface
{
    public function all();

    public function paginate(int $count, string $orderCol = 'id', bool $is_asc = true);

    public function getById(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);
}
