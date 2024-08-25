<?php

use illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

if (!function_exists('calculateFine')) {
    /**
     * Calculate fine based on the difference in days.
     *
     * @param Validator $validator
     * @return array
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
