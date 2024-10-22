<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Reservation\ReservationRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ReservationController extends Controller implements HasMiddleware
{
    protected ReservationRepositoryInterface $repository;

    public function __construct(ReservationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('check_admin', except: ['getByUser']),
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        if ($perPage < 10)
            $perPage = 10;

        $reservations = $this->repository->paginate($perPage);
        return response()->json($reservations, ResponseAlias::HTTP_OK);
    }

    public function getByUser(Request $request): JsonResponse
    {
        $perPage = get_per_page($request);
        $user = $request->user();
        $reservations = $this->repository->getByUser($user->id, $perPage, 'created_at', false);
        return response()->json($reservations, ResponseAlias::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'user_id' => ['required', 'exists:users,id'],
            'book_id' => ['required', 'exists:books,id'],
            'reserved_at' => ['required', 'date'],
            'expires_at' => ['required', 'date'],
        ];
        $validator = Validator::make($request->all(), $rules);
        $validated = validateCreate($validator);
        if ($validated)
            return response()->json($validated['data'], $validated['status']);

        try {
            $validatedData = $validator->validated();
            $data = $this->repository->create($validatedData);
            return response()->json([
                "message" => "You just successfully reserved the book.",
                "data" => $data
            ], ResponseAlias::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while reserving the book: ' . $e->getMessage(),
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
