<?php

namespace App\Http\Controllers;

use App\Models\PumpOperator;
use Illuminate\Http\Request;

class PumpOperatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PumpOperator::with($request->with ?? []);

        if($request->has('station_id')){
            $query->whereStationId($request->input('station_id'));
        }

        if($request->has('search')){
            $query->where('name', 'LIKE', "%{$request->input('search')}%");
        }

        return $query->paginate(perPage: $request->perPage ?? 10, page: $request->page ?? 1);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'contact' => ['required', 'unique:pump_operators,contact'],
            'station_id' => ['required', 'exists:stations,id']
        ]);

        return PumpOperator::create($request->only(['name', 'contact', 'station_id']));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}