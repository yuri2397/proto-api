<?php

namespace App\Http\Controllers;

use App\Models\TankStockFlow;
use Illuminate\Http\Request;

class TankStockFlowController extends Controller
{
    public function index(Request $request)
    {
        $tankStockFlows = TankStockFlow::with($request->input('with') ?? []);

        if ($request->tank_id) {
            $tankStockFlows->where('tank_id', $request->tank_id);
        }

        if ($request->type) {
            $tankStockFlows->where('type', $request->type);
        }

        if ($request->user_id) {
            $tankStockFlows->where('user_id', $request->user_id);
        }

        $tankStockFlows->orderBy('created_at', 'desc');

        $tankStockFlows = $tankStockFlows->paginate(perPage: $request->perPage ?? 10, page: $request->page ?? 1, columns: $request->columns ?? ['*']);

        return response()->json($tankStockFlows);
    }

    public function show(TankStockFlow $tankStockFlow)
    {
        return response()->json($tankStockFlow);
    }
}
