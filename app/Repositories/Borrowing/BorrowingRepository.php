<?php

namespace App\Repositories\Borrowing;

use App\Models\Borrowing;
use App\Repositories\Book\BookRepository;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\User\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class BorrowingRepository implements BorrowingRepositoryInterface
{
    protected Borrowing $model;
    protected UserRepository $userRepository;
    protected CategoryRepository $categoryRepository;
    protected BookRepository $bookRepository;

    function __construct(
        Borrowing          $model,
        UserRepository     $userRepository,
        CategoryRepository $categoryRepository,
        BookRepository     $bookRepository
    )
    {
        $this->model = $model;
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
        $this->bookRepository = $bookRepository;
    }

    public function all(string $orderCol = 'id', bool $is_asc = true): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $count, string $orderCol = 'id', bool $is_asc = true)
    {
        return $this->model->orderBy($orderCol, $is_asc ? 'asc' : 'desc')->paginate($count);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findByUser(int $user_id, int $perPage, string $orderCol = 'id', bool $is_asc = true)
    {
        $user = $this->userRepository->getById($user_id);
        return $user->borrowings()
            ->orderBy($orderCol, $is_asc ? 'asc' : 'desc')
            ->paginate($perPage);
    }

    public function findByCategory(
        int    $category_id,
        int    $perPage,
        string $orderCol = 'id',
        bool   $is_asc = true
    )
    {
        $category = $this->categoryRepository->find($category_id);
        $books = $category->books()->orderBy($orderCol, $is_asc ? 'asc' : 'desc')->get();
        return $books->flatMap(function ($book) {
            return $book->borrowings;
        });
    }

    public function findByBook(
        int    $book_id = 0,
        string $book_slug = '',
        int    $perPage = 10,
        string $orderCol = 'id',
        bool   $is_asc = true
    )
    {
        if ($book_id === 0 && $book_slug === '')
            return [];

        if ($book_id !== 0)
            $book = $this->bookRepository->getById($book_id);
        else
            $book = $this->bookRepository->getBySlug($book_slug);

        if ($book)
            return $book->borrowings()
                ->orderBy($orderCol, $is_asc ? 'asc' : 'desc')
                ->paginate($perPage);
        return [];
    }

    public function create(array $data)
    {
        $quantity = $data["quantity"];
        $can_borrow = $this->bookRepository->borrow($data['book_id'], $quantity);
        if ($can_borrow)
            return $this->model->create($data);
        return false;
    }

    public function update($id, array $data)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function returned(Borrowing $borrow): bool
    {
        $quantity = $borrow->quantity;
        $can_return = $this->bookRepository->returned($borrow->book_id, $quantity);
        if ($can_return)
        {
            $borrow->returned_at = Carbon::today()->toDateString();
            $borrow->save();
            return true;
        }
        return false;

    }
}
