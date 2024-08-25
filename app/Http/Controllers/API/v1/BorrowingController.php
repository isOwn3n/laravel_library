<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Borrowing\BorrowingRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BorrowingController extends Controller
{
    protected BorrowingRepository $repository;

    public function __construct(BorrowingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'admin')
            return response()->json(['message' => 'Not allowed.'], ResponseAlias::HTTP_FORBIDDEN);

        $borrowings = $this->repository->paginate(64, 'created_at', false);
        return response()->json($borrowings, ResponseAlias::HTTP_OK);
    }

    public function borrow(Request $request): JsonResponse
    {
        $rules = [
            'book_id' => 'required|integer|exists:books,id',
            'due_date' => 'required|date',
            'quantity' => 'sometimes|integer',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(
                ['message' => 'Error: ' . $validator->errors()],
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );

        try {
            $validatedData = $validator->validated();
            if (!array_key_exists('quantity', $validatedData)) {
                $validatedData['quantity'] = 1;
            }
            $validatedData['user_id'] = $request->user()->id;
            $borrow = $this->repository->create($validatedData);
            if ($borrow)
                return response()->json([
                    "message" => "Book was created.",
                    "data" => $borrow
                ], ResponseAlias::HTTP_CREATED);
            return response()->json([
                "message" => "Book was not available.",
            ], ResponseAlias::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while borrowing the book: ' . $e->getMessage(),
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function returned(int $id): JsonResponse
    {
        $borrow = $this->repository->find($id);

        if (!$borrow || $borrow->returned_at)
            return response()->json([
                "message" => "Borrow Not Found.",
            ], ResponseAlias::HTTP_NOT_FOUND);

        $is_returned = $this->repository->returned($borrow);
        $fine = calculateFine($borrow->due_date);
        if ($is_returned)
            return response()->json(['message' => 'Book was returned.', 'fine' => $fine], ResponseAlias::HTTP_OK);

        return response()->json(['message' => 'Book can not return.'], ResponseAlias::HTTP_BAD_REQUEST);
    }

    public function getBorrowingsByUser(Request $request): JsonResponse
    {
        $data = $this
            ->repository
            ->findByUser(
                $request
                    ->user()
                    ?->id,
                16, 'created_at', false);
        return response()->json($data, ResponseAlias::HTTP_OK);
    }

    public function getBorrowingsByCategory(int $category_id): JsonResponse
    {
        $data = $this->repository->findByCategory($category_id, 16, 'created_at', false);
        return response()->json($data, ResponseAlias::HTTP_OK);
    }

    public function getBorrowingsByBook(Request $request, int $book_id): JsonResponse
    {
        $user = $request->user();
        $data = $this->repository->findByBook($book_id);

        if ($user?->role !== 'member' || $user?->id === $data?->user_id || $data)
            return response()->json($data, ResponseAlias::HTTP_OK);

        return response()->json($data, ResponseAlias::HTTP_NOT_FOUND);
    }
}
