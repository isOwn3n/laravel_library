<?php

namespace App\Repositories\User;

interface UserRepositoryInterface
{
    public function all();

    public function getById(int $id);

    public function getByEmail(string $email);

    public function getByUsername(string $username);

    public function create(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function plans(int $plan_id, string $orderCol = 'id', bool $is_asc = true);
}
