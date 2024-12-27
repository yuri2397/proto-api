<?php

namespace App\Http\Controllers;

use App\Models\ShopCashRegister;
use Illuminate\Http\Request;

class ShopCashRegisterController extends Controller
{
    // current open cash register 
    public function currentOpenCashRegister()
    {
        // station id of the current user
        $stationId = auth()->user()->owner_id;

        // get current open cash register
        $query = ShopCashRegister::where('status', ShopCashRegister::STATUS_OPEN)->where('station_id', $stationId)->first();

        if (!$query) {
            return $this->jsonResponse(['currentOpenCashRegister' => null]);
        }

        return $this->jsonResponse(['currentOpenCashRegister' => $query]);
    }

    // open cash register
    public function openCashRegister(Request $request)
    {
        $request->validate([
            'starting_balance' => 'required|numeric',
            'open_remarks' => 'nullable|string',
        ]);

        $stationId = auth()->user()->owner_id;

        $cashRegister = ShopCashRegister::where('status', ShopCashRegister::STATUS_OPEN)->where('station_id', $stationId)->first();

        // if cash register is already open, return error
        if ($cashRegister) {
            return $this->jsonResponse(['message' => 'Cash register already open'], 422);
        }

        // create new cash register
        $cashRegister = new ShopCashRegister();
        $cashRegister->station_id = $stationId;
        $cashRegister->starting_balance = $request->starting_balance;
        $cashRegister->open_remarks = $request->open_remarks;
        $cashRegister->opened_by = auth()->user()->id;
        $cashRegister->status = ShopCashRegister::STATUS_OPEN;
        $cashRegister->save();

        return $this->jsonResponse($cashRegister);
    }

    // close cash register
    public function closeCashRegister(Request $request)
    {
        $request->validate([
            'ending_balance' => 'required|numeric',
            'close_remarks' => 'nullable|string',
        ]);

        // get current open cash register
        $cashRegister = $this->currentOpenCashRegister();

        if (!$cashRegister) {
            return response()->json(['message' => 'No open cash register found'], 400);
        }

        $cashRegister->ending_balance = $request->ending_balance;
        $cashRegister->close_remarks = $request->close_remarks;
        $cashRegister->status = ShopCashRegister::STATUS_CLOSED;
        $cashRegister->closed_by = auth()->user()->id;
        $cashRegister->save();

        return $cashRegister;
    }
}
