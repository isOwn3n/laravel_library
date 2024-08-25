<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserRepository implements UserRepositoryInterface
{
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function getById(int $id)
    {
        return $this->model->find($id);
    }

    public function getByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getByUsername(string $username)
    {
        return $this->model->where('username', $username)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id): bool
    {
        return $this->model->update($data, $id);
    }

    public function delete($id): int
    {
        return $this->model->destroy($id);
    }

    public function plans(int $plan_id, string $orderCol = 'id', bool $is_asc = true)
    {
        $users = $this->model->whereHas('membershipPlan', function (Builder $query) use ($plan_id) {
            $query->where('id', $plan_id);
        })->orderBy($orderCol, $is_asc ? 'asc' : 'desc')->get();
        return $users;
    }
}
