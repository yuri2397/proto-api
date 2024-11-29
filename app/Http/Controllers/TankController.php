<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use Illuminate\Http\Request;

class TankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tanks = Tank::with($request->input('with') ?? []); 

        if ($request->station_id) {
            $tanks->where('station_id', $request->station_id);
        }

        if ($request->active) {
            $tanks->where('status', Tank::STATUS_ACTIVE);
        }

        $tanks->orderBy('name', 'asc');

        $tanks = $tanks->paginate($request->per_page ?? 10);    

        return $this->jsonResponse($tanks, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Tank $tank)
    {
        return $tank->load($request->with ?? []);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tank $tank)
    {
        $request->validate([
            'name' => 'string|max:255',
            'type' => 'string|in:'.implode(',', [Tank::GASOLINE_TYPE, Tank::DIESEL_TYPE]),
            'capacity' => 'numeric|min:0',
            'status' => 'string|in:'.implode(',', [Tank::STATUS_ACTIVE, Tank::STATUS_INACTIVE]),
            
        ]);

        // value is not changed
        if ($tank->update($request->only(['name', 'type', 'capacity', 'status']))) {
            return response()->json(['message' => 'Tank updated successfully'], 200);
        }

        return response()->json(['message' => 'Failed to update tank'], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function addNewPump(Tank $tank, Request $request)
    {
        $request->validate([
            'tank_id' => 'required|exists:tanks,id',
            'station_id' => 'required|exists:stations,id',
        ]);

        $tank->pumps()->create($request->only(['tank_id', 'station_id']));

        return $this->jsonResponse(['message' => 'Pump added successfully', 'pump' => $tank->load('pumps')], 200);
    }

}
