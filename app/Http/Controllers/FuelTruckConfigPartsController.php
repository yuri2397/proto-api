<?php

namespace App\Http\Controllers;

use App\Models\FuelTruckConfigPart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuelTruckConfigPartsController extends Controller
{
    // index    
    public function index(Request $request)
    {
        $query = FuelTruckConfigPart::with($request->with ?? []);

        if ($request->has('tank_id')) {
            $query->where('tank_id', $request->tank_id);
        }

        $query = $query->paginate(perPage: $request->perPage ?? 10, page: $request->page ?? 1, columns: $request->columns ?? ['*']);
        return response()->json($query);
    }


    public function update(Request $request, FuelTruckConfigPart $fuelTruckConfigPart)
    {
        $request->validate([
            'received_quantity' => 'required|numeric|min:0',
            'quantity_before_delivery' => 'required|numeric|min:0',
            'quantity_after_delivery' => 'required|numeric|min:0',
        ]);
        $request->merge(['quantity_difference' => $fuelTruckConfigPart->quantity - $request->received_quantity]);

        DB::beginTransaction();
        try {
            $fuelTruckConfigPart->update($request->all());

            \App\Models\TankStockFlow::create([
                'quantity' => $request->received_quantity,
                'type' => 'received',
                'user_id' => auth()->id(),
                'previous_quantity' => $fuelTruckConfigPart->tank->current_quantity,
                'tank_id' => $fuelTruckConfigPart->tank_id,
                'dataable_type' => FuelTruckConfigPart::class,
                'dataable_id' => $fuelTruckConfigPart->id,
                'data' => $fuelTruckConfigPart->toArray(),
            ]);

            $fuelTruckConfigPart->tank->addQuantity($request->received_quantity);

            DB::commit();
            return response()->json($fuelTruckConfigPart);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'error' => true], 500);
        }
    }

    
}
