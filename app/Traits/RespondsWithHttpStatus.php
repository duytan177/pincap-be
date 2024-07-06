<?php

namespace App\Http\Traits;

trait RespondsWithHttpStatus
{
    protected function success($message, $data = [], $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function failure($message, $status = 500)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
