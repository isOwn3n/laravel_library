<?php

namespace App\Repositories\Book;

use App\Repositories\BaseRepositoryInterface;

interface BookRepositoryInterface extends BaseRepositoryInterface
{
    public function getBySlug(string $slug);

    public function borrow(int $id, int $quantity);

    public function returned(int $id, int $quantity);

    public function updateBySlug(string $slug, array $data);

    public function deleteBySlug(string $slug);

    public function search(string $search, string $search_on);
}
