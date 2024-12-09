<?php

namespace App\Http\Controllers;

use App\Models\FuelTruckConfig;
use App\Models\FuelTruckConfigPart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuelTruckConfigController extends Controller
{

    // index
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FuelTruckConfig::with($request->with ?? []);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->reference) {
            $query->where('reference', 'like', '%' . $request->reference . '%');
        }

        // station_id 
        if ($request->station_id) {
            $query->whereHas('fuelTruckConfigParts', function ($query) use ($request) {
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
            'fuel_truck_config_parts' => 'required|array',
            'fuel_truck_config_parts.*.type' => 'required|string',
            'fuel_truck_config_parts.*.quantity' => 'required|numeric|min:0',
            'fuel_truck_config_parts.*.name' => 'required|string',
            'fuel_truck_config_parts.*.number' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {

            $fuelTruck = \App\Models\FuelTruck::create([
                'matricule' => $request->fuel_truck_plate_number,
            ]);

            $fuelTruckDriver = \App\Models\FuelTruckDriver::create([
                'name' => $request->driver_name,
                'phone' => $request->driver_phone,
            ]);

            $total_quantity = collect($request->fuel_truck_config_parts)->sum('quantity');

            $config = \App\Models\FuelTruckConfig::create([
                'total_quantity' => $total_quantity,
                'fuel_truck_id' => $fuelTruck->id,
                'fuel_truck_driver_id' => $fuelTruckDriver->id,
            ]);

            foreach ($request->fuel_truck_config_parts as $part) {
                $config->fuelTruckConfigParts()->create($part);
            }


            DB::commit();
            return response()->json([
                'message' => 'Station fuel order created successfully',
                'data' => $config,
            ], 201);
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
    public function show(FuelTruckConfig $fuelTruckConfig, Request $request)
    {

        $config = FuelTruckConfig::with([
            'fuelTruckConfigParts' => function ($q) use ($request) {
                if ($request->station_id) {
                    $q->where('station_id', $request->station_id);
                }
            },
            ...$request->with ?? []
        ])->find($fuelTruckConfig->id);

        return response()->json($config);
    }


    // update
    public function update(Request $request, FuelTruckConfig $fuelTruckConfig)
    {
        $request->validate([
            'status' => 'required|string|in:' . implode(',', FuelTruckConfig::STATUSES),
        ]);

        $fuelTruckConfig->update(['status' => $request->status]);

        return response()->json($fuelTruckConfig);
    }

}
