<?php

namespace App\Repositories\Book;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

class BookRepository implements BookRepositoryInterface
{
    protected Book $model;

    public function __construct(Book $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $count, string $orderCol = 'id', bool $is_asc = true)
    {
        return $this->model->orderBy($orderCol, $is_asc ? 'asc' : 'desc')->paginate($count);
    }

    public function getById(int $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function borrow(int $id, int $quantity): bool
    {
        $book = $this->getById($id);
        if ($book && $book->available_quantity >= $quantity) {
            $book->available_quantity = max(0, $book->available_quantity - $quantity);
            $book->save();
            return true;
        }
        return false;
    }

    public function returned(int $id, int $quantity): bool
    {
        $book = $this->getById($id);
        if ($book && $book->available_quantity <= $book->quantity) {
            $book->available_quantity = min(
                $book->quantity,
                max(0, $book->available_quantity + $quantity)
            );
            $book->save();
            return true;
        }
        return false;
    }

    public function deleteBySlug(string $slug)
    {
        $book = $this->getBySlug($slug);
        return $book->delete();
    }

    public function getBySlug(string $slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function delete($id): void
    {
        $this->model->destroy($id);
    }

    public function updateBySlug(string $slug, array $data)
    {
        $book = $this->getBySlug($slug);
        return $book->update($data);
    }

    public function update($id, array $data)
    {
        return $this->model->where('id', $id)->update($data);
    }

    // TODO: Convert $search_on to array
    public function search(string $search, string $search_on)
    {
        return $this->model->where($search_on, 'like', "%$search%")->get();
    }
}
