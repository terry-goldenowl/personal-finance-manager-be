<?php

namespace App\Http\Helpers;

class ReturnType
{
    public static function success(string $message = "", array $data = [])
    {
        return [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ];
    }

    public static function fail($error)
    {
        return [
            'status' => 'fail',
            'error' => $error
        ];
    }

    public static function response(array $successData)
    {
        return response()->json($successData);
    }
}
