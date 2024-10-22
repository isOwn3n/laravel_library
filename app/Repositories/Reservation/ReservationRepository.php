<?php

namespace App\Repositories\Reservation;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ReservationRepository implements ReservationRepositoryInterface
{
    protected Reservation $model;

    public function __construct(Reservation $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * @param int $count
     * @param string $orderCol = 'id'
     * @param bool $is_asc = true
     * @return LengthAwarePaginator
     */
    public function paginate(int $count, string $orderCol = 'id', bool $is_asc = true): LengthAwarePaginator
    {
        return $this->model->orderBy($orderCol, $is_asc ? 'asc' : 'desc')->paginate($count);
    }

    /**
     * @param int $id
     * @return Reservation | null
     */
    public function getById(int $id): ?Reservation
    {
        return $this->model->find($id);
    }

    /**
     * @param array $data
     * @return Reservation
     */
    public function create(array $data): Reservation
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $reservation = $this->getById($id);
        return $reservation->update($data);
    }

    /**
     * @param int $id
     * @return bool|null
     */
    public function delete(int $id): ?bool
    {
        $reservation = $this->getById($id);
        return $reservation->delete();
    }

    /**
     * @param int $userId
     * @param int $perPage
     * @param string $orderCol
     * @param bool $is_asc
     * @return LengthAwarePaginator
     */
    public function getByUser(int $userId, int $perPage = 16, string $orderCol = 'id', bool $is_asc = true): LengthAwarePaginator
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy($orderCol, $is_asc ? 'asc' : 'desc')
            ->paginate($perPage);
    }
}
