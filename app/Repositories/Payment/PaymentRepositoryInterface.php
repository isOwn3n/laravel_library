<?php

namespace App\Repositories\Payment;

use App\Repositories\BaseRepositoryInterface;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function getByUser(int $userId, int $perPage = 16, string $orderCol = 'id', bool $is_asc = true);
}
