<?php

namespace App\Http\Controllers;

use App\Models\StationFuelOrder;
use Illuminate\Http\Request;

class StationFuelOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StationFuelOrder::with($request->with ?? []);
        
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->reference) {
            $query->where('reference', 'like', '%' . $request->reference . '%');
        }

        return response()->json($query->paginate($request->perPage ?? 10, ['*'], 'page', $request->page ?? 1));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'stations_ids' => 'required|array',
            'stations_ids.*' => 'required|exists:stations,id',
            'fuel_truck_config_parts' => 'required|array',
            'fuel_truck_config_parts.*.type' => 'required|string',
            'fuel_truck_config_parts.*.capacity' => 'required|numeric|min:0',
            'fuel_truck_config_parts.*.quantity' => 'required|numeric|min:0',
        ]);
        try {
            $config = \App\Models\FuelTruckConfig::create($request->only('total_quantity', 'total_amount'));

            foreach ($request->fuel_truck_config_parts as $part) {
                $config->fuelTruckConfigParts()->create($part);
            }

            $totalQuantity = $config->fuelTruckConfigParts()->sum('capacity');

            $stationFuelOrder = \App\Models\StationFuelOrder::create([
                'fuel_truck_config_id' => $config->id,
                'reference' => \Illuminate\Support\Str::uuid(),
                'status' => 'initiated',
                'data' => $request->all(),
                'quantity' => $totalQuantity,
            ]);

            return response()->json([
                'message' => 'Station fuel order created successfully',
                'data' => $stationFuelOrder,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error creating station fuel order',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StationFuelOrder $stationFuelOrder, Request $request)
    {
        return response()->json($stationFuelOrder->load($request->with ?? []));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StationFuelOrder $stationFuelOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StationFuelOrder $stationFuelOrder)
    {
        //
    }
}
