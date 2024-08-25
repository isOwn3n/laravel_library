<?php

namespace App\Repositories\Borrowing;

use App\Models\Borrowing;

interface BorrowingRepositoryInterface
{
    public function all(string $orderCol = 'id', bool $is_asc = true);

    public function paginate(int $count, string $orderCol = 'id', bool $is_asc = true);

    public function find($id);

    public function findByUser(int $user_id, int $perPage, string $orderCol = 'id', bool $is_asc = true);

    public function findByCategory(int $category_id, int $perPage, string $orderCol = 'id', bool $is_asc = true);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function returned(Borrowing $borrow);
}
