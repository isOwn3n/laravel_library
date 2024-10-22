<?php

namespace App\Repositories\MembershipPlan;

use App\Repositories\BaseRepositoryInterface;

interface MembershipPlanRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllUsers(int $id, string $orderCol = 'id', bool $is_asc = true);
}
