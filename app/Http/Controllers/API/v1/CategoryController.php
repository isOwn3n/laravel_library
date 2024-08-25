<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Category\CategoryRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CategoryController extends Controller
{
    protected CategoryRepository $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(): JsonResponse
    {
        $categories = $this->repository->paginate(16, 'created_at', false);
        return response()->json($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(
                ['message' => 'Error: ' . $validator->errors()],
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );

        try {
            $validatedData = $validator->validated();
            $category = $this->repository->create($validatedData);
            return response()->json([
                "message" => "Category was created.",
                "data" => $category
            ], ResponseAlias::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the category: ' . $e->getMessage(),
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->repository->find($id);
        if (is_null($category)) {
            return response()->json([
                "message" => "Category not found."
            ], ResponseAlias::HTTP_NOT_FOUND);
        }
        return response()->json(["data" => $category]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $rules = [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
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

        $does_update = $this->repository->update($data, $id);
        if ($does_update)
            return response()->json([
                "message" => "Category was updated.",
            ]);
        else
            return response()->json([
                "message" => "Category was not updated.",
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        return response()->json($deleted, ResponseAlias::HTTP_NO_CONTENT);
    }
}
