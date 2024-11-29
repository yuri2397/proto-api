<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function jsonResponse(mixed $response, $status = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json($response, $status);
    }
}
