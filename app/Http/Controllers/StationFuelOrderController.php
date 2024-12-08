<?php

namespace App\Http\Controllers;

use App\Models\StationFuelOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Spatie\LaravelPdf\Support\pdf;

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

        // station_id 
        if ($request->station_id) {
            $query->whereHas('stationFuelOrderItems', function ($query) use ($request) {
                $query->where('station_id', $request->station_id);
            });
        }

        return response()->json($query->paginate($request->perPage ?? 10, ['*'], 'page', $request->page ?? 1));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fuel_truck_plate_number' => 'required|string',
            'driver_name' => ['required', 'string', 'max:255'],
            'driver_phone' => ['string', 'max:255'],
            'stations_ids' => 'required|array',
            'stations_ids.*' => 'required|exists:stations,id',
            'fuel_truck_config_parts' => 'required|array',
            'fuel_truck_config_parts.*.type' => 'required|string',
            'fuel_truck_config_parts.*.quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {

            // create fuel truck 
            $fuelTruck = \App\Models\FuelTruck::create([
                'matricule' => $request->fuel_truck_plate_number,
            ]);

            // create fuel truck driver
            $fuelTruckDriver = \App\Models\FuelTruckDriver::create([
                'name' => $request->driver_name,
                'phone' => $request->driver_phone,
            ]);

            // create fuel truck config
            $config = \App\Models\FuelTruckConfig::create([
                'total_quantity' => $request->total_quantity,
                'fuel_truck_id' => $fuelTruck->id,
                'fuel_truck_driver_id' => $fuelTruckDriver->id,
            ]);

            foreach ($request->fuel_truck_config_parts as $part) {
                $config->fuelTruckConfigParts()->create($part);
            }

            $totalQuantity = $config->fuelTruckConfigParts()->sum('quantity');
            
            $stationFuelOrder = \App\Models\StationFuelOrder::create([
                'fuel_truck_config_id' => $config->id,
                'status' => 'initiated',
                'data' => $request->all(),
                'quantity' => $totalQuantity,
            ]);

            foreach ($request->stations_ids as $stationId) {
                $stationFuelOrder->stationFuelOrderItems()->create([
                    'station_id' => $stationId,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Station fuel order created successfully',
                'data' => $stationFuelOrder,
            ], 500);
        } catch (\Throwable $th) {
            DB::rollBack();
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
        $request->validate([
            'status' => 'required|string|in:' . implode(',', StationFuelOrder::STATUSES),
        ]);

        $stationFuelOrder->update(['status' => $request->status]);

        return response()->json($stationFuelOrder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StationFuelOrder $stationFuelOrder)
    {
        //
    }

    public function downloadPdf(StationFuelOrder $stationFuelOrder)
    {
        return view('pdf.station-fuel-order', compact('stationFuelOrder'));
    }
}
