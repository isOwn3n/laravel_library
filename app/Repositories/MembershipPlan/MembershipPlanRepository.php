<?php

namespace App\Repositories\MembershipPlan;

use App\Models\MembershipPlan;
use App\Repositories\User\UserRepository;

class MembershipPlanRepository implements MembershipPlanRepositoryInterface
{

    protected MembershipPlan $model;
    protected UserRepository $userRepository;

    public function __construct(MembershipPlan $membershipPlan, UserRepository $userRepository)
    {
        $this->model = $membershipPlan;
        $this->userRepository = $userRepository;
    }

    public function all(string $orderCol = 'id', bool $is_asc = true)
    {
        return $this->model->orderBy($orderCol, $is_asc ? 'asc' : 'desc')->all();
    }

    public function getAllUsers(int $id, string $orderCol = 'id', bool $is_asc = true)
    {
        return $this->userRepository->plans($id, $orderCol, $is_asc);
    }

    public function paginate(int $count, string $orderCol = 'id', bool $is_asc = true)
    {
        return $this->model->orderBy($orderCol, $is_asc ? 'asc' : 'desc')->paginate($count);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, int $id)
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }
}
