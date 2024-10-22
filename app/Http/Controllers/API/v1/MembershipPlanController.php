<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\MembershipPlan\MembershipPlanRepositoryInterface;
use App\Services\GlobalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MembershipPlanController extends Controller implements HasMiddleware
{
    protected MembershipPlanRepositoryInterface $repository;
    protected GlobalService $global_service;

    public function __construct(
        MembershipPlanRepositoryInterface $repository,
        GlobalService                     $global_service
    )
    {
        $this->repository = $repository;
        $this->global_service = $global_service;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('check_admin', except: ['index']),
        ];
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

    /**
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'max_books_allowed' => ['required', 'integer', 'min:3'],
            'borrow_duration_days' => ['required', 'integer', 'min:14'],
            'price' => ['required', 'integer', 'min:0'],
        ];

        $data = $request->all();
        $validator = $this->global_service->create($data, $rules);
        if ($validator['status'] === ResponseAlias::HTTP_UNPROCESSABLE_ENTITY)
            return response()->json($validator['data'], $validator['status']);
//        $validator = Validator::make($request->all(), $rules);
//        if ($validator->fails())
//            return response()->json(
//                ['message' => 'Error: ' . $validator->errors()],
//                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
//            );
        $validatedData = $validator['data'];
        try {
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

    /**
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        if ($id === 1)
            return response()->json(['message' => 'You cannot edit Free Plan.'], ResponseAlias::HTTP_FORBIDDEN);
        $rules = [
            'name' => ['sometimes', 'string', 'max:255'],
            'max_books_allowed' => ['sometimes', 'integer', 'min:3'],
            'borrow_duration_days' => ['sometimes', 'integer', 'min:14'],
            'price' => ['sometimes', 'integer', 'min:0'],
        ];
        $validator = Validator::make($request->all(), $rules);
        $validatedData = validateUpdate($validator);
        if ($validatedData['status'] !== 0)
            return response()->json([
                'message' => $validatedData['data'],
            ], $validatedData['status']);

        $does_update = $this->repository->update($id, $validatedData['data']);
        if ($does_update)
            return response()->json([
                "message" => "Plan was updated.",
            ]);
        return response()->json([
            "message" => "Plan was not updated.",
        ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function destroy(int $id): JsonResponse
    {
        if ($id === 1)
            return response()->json([
                'message' => "You cannot delete Free Plan."
            ], ResponseAlias::HTTP_FORBIDDEN);
        $deleted = $this->repository->delete($id);
        return response()->json($deleted, ResponseAlias::HTTP_NO_CONTENT);
    }
}
