<?php

namespace App\Repositories\Book;

interface BookRepositoryInterface
{
    public function getAll();

    public function paginate(int $count, string $orderCol = 'id', bool $is_asc = true);

    public function getById(int $id);

    public function getBySlug(string $id);

    public function borrow(int $id, int $quantity);

    public function returned(int $id, int $quantity);

    public function create(array $data);

    public function update($id, array $data);

    public function updateBySlug(string $slug, array $data);

    public function delete($id);

    public function deleteBySlug(string $slug);
}
