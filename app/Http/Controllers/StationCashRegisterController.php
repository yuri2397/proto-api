<?php

namespace App\Http\Controllers;

use App\Models\StationCashRegister;
use Illuminate\Http\Request;

class StationCashRegisterController extends Controller
{
    public function index(Request $request){
        $request->validate([
            'station_id' => 'exists:stations,id',
        ]);

        $query = StationCashRegister::with($request->with ?? []);

        if($request->station_id){
            $query->where('station_id', $request->station_id);
        }

        return $query->paginate(perPage: $request->perPage ?? 10, page: $request->page ?? 1);
    }
}
