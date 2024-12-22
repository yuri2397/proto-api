<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function jsonResponse(mixed $response, $status = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json($response, $status);
    }

    protected function paginate($query, $perPage = 10, $page = 1, $columns = ['*'])
    {
        return $query->paginate($perPage, $columns, 'page', $page);
    }
}
