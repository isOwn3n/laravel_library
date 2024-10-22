<?php

namespace App\Services;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BorrowingService
{
    public function handleBorrowingUserValidation(array $validatedData, int $total_borrowings, $user): array
    {
        $plan = $user->membershipPlan;
        if (
            $plan->max_books_allowed < $total_borrowings ||
            $validatedData['quantity'] + $total_borrowings > $plan->max_books_allowed
        )
            return [
                'data' => ['message' => 'You Cannot Borrow Another Book Until Returning The Other Books.'],
                'status' => ResponseAlias::HTTP_FORBIDDEN,
                'type' => 'error',
            ];
        $borrowDuration = Carbon::now()->addDays($plan->borrow_duration_days);

        $validatedData['user_id'] = $user->id;
        $validatedData['due_date'] = $borrowDuration;
        return [
            'data' => $validatedData,
            'type' => 'data'
        ];
    }
}
