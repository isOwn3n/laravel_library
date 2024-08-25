<?php

namespace App\Repositories\MembershipPlan;

interface MembershipPlanRepositoryInterface
{
    public function all(string $orderCol = 'id', bool $is_asc = true);

    public function paginate(int $count, string $orderCol = 'id', bool $is_asc = true);

    public function getAllUsers(int $id, string $orderCol = 'id', bool $is_asc = true);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);
}
