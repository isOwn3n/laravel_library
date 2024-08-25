<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\MembershipPlan\MembershipPlanRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MembershipPlanController extends Controller
{
    protected MembershipPlanRepositoryInterface $repository;

    public function __construct(MembershipPlanRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(): JsonResponse
    {
        $plans = $this->repository->paginate(6, 'created_at', false);
        return response()->json($plans, ResponseAlias::HTTP_OK);
    }

    public function getUsersById(int $id): JsonResponse
    {
        return response()->json($this->repository->getAllUsers($id));
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'max_books_allowed' => ['required', 'integer', 'min:3'],
            'borrow_duration_days' => ['required', 'integer', 'min:14'],
            'price' => ['required', 'integer', 'min:0'],
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
                "message" => "Plan was created.",
                "data" => $category
            ], ResponseAlias::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the plan: ' . $e->getMessage(),
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $rules = [
            'name' => ['sometimes', 'string', 'max:255'],
            'max_books_allowed' => ['sometimes', 'integer', 'min:3'],
            'borrow_duration_days' => ['sometimes', 'integer', 'min:14'],
            'price' => ['sometimes', 'integer', 'min:1000'],
        ];
        $validator = Validator::make($request->all(), $rules);
        $validatedData = validateUpdate($validator);
        if ($validatedData['status'] !== 0)
            return response()->json([
                'message' => $validatedData['data'],
            ], $validatedData['status']);

        $does_update = $this->repository->update($validatedData['data'], $id);
        if ($does_update)
            return response()->json([
                "message" => "Plan was updated.",
            ]);
        else
            return response()->json([
                "message" => "Plan was not updated.",
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        return response()->json($deleted, ResponseAlias::HTTP_NO_CONTENT);
    }
}
