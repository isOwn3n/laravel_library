<?php

namespace App\Repositories\Payment;

use App\Models\Payment;

class PaymentRepository implements PaymentRepositoryInterface
{
    protected Payment $model;

    public function __construct(Payment $model)
    {
        $this->model = $model;
    }

    public function all(string $orderCol = 'id', bool $is_asc = true)
    {
        return $this->model->orderBy($orderCol, $is_asc ? 'asc' : 'desc')->all();
    }

    public function paginate(int $count, string $orderCol = 'id', bool $is_asc = true)
    {
        return $this->model->orderBy($orderCol, $is_asc ? 'asc' : 'desc')->paginate($count);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function delete(int $id): void
    {
        $this->model->where('id', $id)->delete();
    }

    public function getById(int $id)
    {
        return $this->model->find($id);
    }

    public function getByUser(int $userId, int $perPage=16, string $orderCol = 'id', bool $is_asc = true)
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy($orderCol, $is_asc ? 'asc' : 'desc')
            ->paginate($perPage);
    }
}
