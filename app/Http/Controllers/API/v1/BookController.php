<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Book\BookRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BookController extends Controller
{
    protected BookRepositoryInterface $repository;

    public function __construct(BookRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request): JsonResponse
    {
        $search = $request->get('q', '');
        $search_on = $request->get('field', '');
        if (!empty($search))
            $books = $this->repository->search($search, empty($search_on) ? 'title' : $search_on);
        else
            $books = $this->repository->paginate(16, 'created_at', false);
        return response()->json($books, ResponseAlias::HTTP_OK);
    }

    public function show(string $slug): JsonResponse
    {
        $book = $this->repository->getBySlug($slug);
        return response()->json($book, ResponseAlias::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:13', 'unique:books,isbn'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'available_quantity' => ['required', 'integer', 'min:0', 'lte:quantity'],
            'description' => ['sometimes', 'nullable', 'string'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(
                ['message' => 'Error: ' . $validator->errors()],
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );

        try {
            $validatedData = $validator->validated();
            $book = $this->repository->create($validatedData);
            return response()->json([
                "message" => "Book was created.",
                "data" => $book
            ], ResponseAlias::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the book: ' . $e->getMessage(),
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, string $slug): JsonResponse
    {
        $rules = [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            /*'due_date' => 'sometimes|date',*/
            'category_id' => 'sometimes|integer',
            /*'status' => 'sometimes|boolean',*/
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return response()->json(
                ['message' => 'Error: ' . $validator->errors()],
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );

        $data = $validator->validated();
        if (empty($data))
            return response()->json(
                ['error' => 'You must provide at least one field to update.'],
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        // Update By ID.
        //        $this->repository->update($book_id, $data);

        // Update By Slug.
        $this->repository->updateBySlug($slug, $data);
        return response()->json(['message' => 'Book was updated.'], ResponseAlias::HTTP_OK);
    }

    public function destroy(string $slug): JsonResponse
    {
        $this->repository->deleteBySlug($slug);
        return response()->json(
            ['message' => 'Book was deleted.'],
            ResponseAlias::HTTP_NO_CONTENT
        );
    }
}
