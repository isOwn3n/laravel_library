<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Payment\PaymentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PaymentController extends Controller implements HasMiddleware
{
    protected PaymentRepositoryInterface $repository;

    public function __construct(PaymentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('check_admin', except: ['me']),
        ];
    }

    public function getByUser(Request $request): JsonResponse
    {
        $user = $request->user();
        $payments = $this->repository->getByUser($user->id, 10, 'created_at', false);
        return response()->json($payments, ResponseAlias::HTTP_OK);
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = get_per_page($request);
        $payments = $this->repository->paginate($perPage);
        return response()->json($payments, ResponseAlias::HTTP_OK);
    }
}
