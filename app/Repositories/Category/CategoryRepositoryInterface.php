<?php

namespace App\Repositories\Category;

interface CategoryRepositoryInterface
{
    public function all(string $orderCol = 'id', bool $is_asc = true);

    public function paginate(int $count, string $orderCol = 'id', bool $is_asc = true);

    public function find(int $id);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);
}
