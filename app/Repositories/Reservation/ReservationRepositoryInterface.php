<?php

namespace App\Repositories\Reservation;

use App\Repositories\BaseRepositoryInterface;

interface ReservationRepositoryInterface extends BaseRepositoryInterface {
    public function getByUser(int $userId, int $perPage = 16, string $orderCol = 'id', bool $is_asc = true);
}
