<?php

use Illuminate\Http\Request;
use illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

if (!function_exists('validateUpdate')) {
    /**
     * Calculate fine based on the difference in days.
     *
     * @param Validator $validator
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    function validateUpdate(Validator $validator): array
    {
        if ($validator->fails())
            return [
                'data' => ['message' => 'Error: ' . $validator->errors()],
                'status' => ResponseAlias::HTTP_BAD_REQUEST
            ];

        $data = $validator->validated();

        if (empty($data))
            return [
                'data' => ['error' => 'You must provide at least one field to update.'],
                'status' => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            ];

        return ['data' => $data, 'status' => 0];
    }
}

if (!function_exists('get_per_page')) {
    /**
     * @param Request $request
     * @return int
     */
    function get_per_page(Request $request): int
    {
        $perPage = ($request->query('per_page', 10));
        if ($perPage < 10)
            $perPage = 10;
        return $perPage;
    }
}

if (!function_exists('validateCreate')) {
    function validateCreate(Validator $validator): ?array
    {
        if ($validator->fails())
            return [
                'data' => ['message' => 'Error: ' . $validator->errors()],
                'status' => ResponseAlias::HTTP_BAD_REQUEST
            ];
        return null;
    }
}
