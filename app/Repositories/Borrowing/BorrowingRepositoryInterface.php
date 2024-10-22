<?php

namespace App\Repositories\Borrowing;

use App\Models\Borrowing;
use App\Repositories\BaseRepositoryInterface;

interface BorrowingRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUser(int $user_id, int $perPage, string $orderCol = 'id', bool $is_asc = true);

    public function findByCategory(int $category_id, int $perPage, string $orderCol = 'id', bool $is_asc = true);

    public function returned(Borrowing $borrow);
}
