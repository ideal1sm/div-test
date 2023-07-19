<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait RespondsWithHttpStatus
{
    protected function success($data = [], $message = '', $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    protected function failure($message, $status = 422) : JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
