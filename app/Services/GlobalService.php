<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class GlobalService
{
    /**
     * @throws ValidationException
     */
    public function create($data, array $rules): array
    {
        $validator = Validator::make($data, $rules);
        if ($validator->fails())
            return [
                'data' => 'Error: ' . $validator->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ];
        return [
            'data' => $validator->validated(),
            'status' => Response::HTTP_CREATED
        ];
    }
}
