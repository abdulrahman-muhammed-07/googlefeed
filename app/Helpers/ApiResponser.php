<?php

namespace App\Helpers;

class ApiResponser
{
    public static function success(array $data)
    {
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public static function fail($th, $code = 500)
    {
        return response()->json(
            [
                'status' => 'error',
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ],
            $code
        );
    }
}
