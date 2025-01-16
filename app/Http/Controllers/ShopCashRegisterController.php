<?php

namespace App\Http\Controllers;

use App\Models\ShopCashRegister;
use App\Models\ShopSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShopCashRegisterController extends Controller
{
    // indext 
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string'],
            'perPage' => ['required', 'numeric'],
            'page' => ['required', 'numeric'],
        ]);

        $query = ShopCashRegister::with(['openedBy', 'closedBy'])->orderBy('id', 'desc');

        $paginate = $this->paginate($query, $request->perPage, $request->page);
        return $this->jsonResponse($paginate);
    }

    public function dashboard() 
    {
        // total sales paid
        $totalSalesPaid = ShopSale::where('status', ShopSale::STATUS_PAID)->sum('total_amount');
        // count of sales paid
        $totalSalesPaidCount = ShopSale::where('status', ShopSale::STATUS_PAID)->count();

        // total sales paid cash 
        $totalSalesPaidCash = ShopSale::where('status', ShopSale::STATUS_PAID)->where('payment_method', ShopSale::PAYMENT_METHOD_CASH)->sum('total_amount');
        // count of sales paid cash
        $totalSalesPaidCashCount = ShopSale::where('status', ShopSale::STATUS_PAID)->where('payment_method', ShopSale::PAYMENT_METHOD_CASH)->count();

        // total sales paid wave
        $totalSalesPaidWave = ShopSale::where('status', ShopSale::STATUS_PAID)->where('payment_method', ShopSale::PAYMENT_METHOD_WAVE)->sum('total_amount');
        // count of sales paid wave
        $totalSalesPaidWaveCount = ShopSale::where('status', ShopSale::STATUS_PAID)->where('payment_method', ShopSale::PAYMENT_METHOD_WAVE)->count();

        // total sales paid om
        $totalSalesPaidOm = ShopSale::where('status', ShopSale::STATUS_PAID)->where('payment_method', ShopSale::PAYMENT_METHOD_OM)->sum('total_amount');
        // count of sales paid om
        $totalSalesPaidOmCount = ShopSale::where('status', ShopSale::STATUS_PAID)->where('payment_method', ShopSale::PAYMENT_METHOD_OM)->count();

        // total sales paid other
        $totalSalesPaidOther = ShopSale::where('status', ShopSale::STATUS_PAID)->where('payment_method', ShopSale::PAYMENT_METHOD_OTHER)->sum('total_amount');
        // count of sales paid other
        $totalSalesPaidOtherCount = ShopSale::where('status', ShopSale::STATUS_PAID)->where('payment_method', ShopSale::PAYMENT_METHOD_OTHER)->count();

        return $this->jsonResponse([
            'totalSalesPaid' => $totalSalesPaid,
            'totalSalesPaidCount' => $totalSalesPaidCount,
            'totalSalesPaidCash' => $totalSalesPaidCash,
            'totalSalesPaidCashCount' => $totalSalesPaidCashCount,
            'totalSalesPaidWave' => $totalSalesPaidWave,
            'totalSalesPaidWaveCount' => $totalSalesPaidWaveCount,
            'totalSalesPaidOm' => $totalSalesPaidOm,
            'totalSalesPaidOmCount' => $totalSalesPaidOmCount,
            'totalSalesPaidOther' => $totalSalesPaidOther,
            'totalSalesPaidOtherCount' => $totalSalesPaidOtherCount,
        ]);
    }


    public function salesEvolutions(Request $request)
    {
        // Validation du champ date
        $validated = $request->validate([
            'date' => 'nullable|date',
        ]);

        // Utiliser la date du jour par défaut
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();

        // Obtenir le nombre de jours dans le mois
        $daysInMonth = $date->daysInMonth;

        // Initialiser un tableau avec des zéros
        $salesByDay = array_fill(0, $daysInMonth, 0);

        // Récupérer les ventes pour le mois et année spécifiés
        $sales = ShopSale::whereYear('created_at', $date->year)
        ->whereMonth('created_at', $date->month)
        ->where('status', ShopSale::STATUS_PAID)
        ->select(DB::raw('DAY(created_at) as day'), DB::raw('COUNT(*) as count'))
        ->groupBy('day')
        ->get();

        // Mettre à jour le tableau avec les ventes
        foreach ($sales as $sale) {
            // Remplir le tableau en utilisant le jour comme index
            $salesByDay[$sale->day - 1] = $sale->count; // -1 car l'index commence à 0
        }

        // Retourner les données sous forme de réponse JSON
        return $this->jsonResponse($salesByDay);
    }

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

        // station id of the current user
        $stationId = auth()->user()->owner_id;

        $currentOpenCashRegister = ShopCashRegister::where('status', ShopCashRegister::STATUS_OPEN)->where('station_id', $stationId)->first();

        if (!$currentOpenCashRegister) {
            return $this->jsonResponse(['message' => 'No open cash register found'], 400);
        }

        $currentOpenCashRegister->ending_balance = $request->ending_balance;
        $currentOpenCashRegister->close_remarks = $request->close_remarks;
        $currentOpenCashRegister->status = ShopCashRegister::STATUS_CLOSED;
        // difference
        $currentOpenCashRegister->difference = ($currentOpenCashRegister->total_cash_sale - (int)$request->ending_balance);
        $currentOpenCashRegister->closed_by = auth()->user()->id;
        $currentOpenCashRegister->save();

        return $this->jsonResponse($currentOpenCashRegister);
    }

    // current cash register details
    public function currentCashRegisterDetails($cashRegisterId)
    {
        $cashRegister = ShopCashRegister::find($cashRegisterId);

        // total sales
        $totalSales = ShopSale::where('cash_register_id', $cashRegisterId)->sum('total_amount');

        // total cash sale amount
        $totalCashSale = ShopSale::where('cash_register_id', $cashRegisterId)
            ->where('payment_method', ShopSale::PAYMENT_METHOD_CASH)
            ->sum('total_amount');

        // total wave sale amount
        $totalWaveSale = ShopSale::where('cash_register_id', $cashRegisterId)
            ->where('payment_method', ShopSale::PAYMENT_METHOD_WAVE)
            ->sum('total_amount');

        // total om sale amount
        $totalOmSale = ShopSale::where('cash_register_id', $cashRegisterId)
            ->where('payment_method', ShopSale::PAYMENT_METHOD_OM)
            ->sum('total_amount');

        return $this->jsonResponse([
            'totalSales' => $totalSales,
            'totalCashSale' => $totalCashSale,
            'totalWaveSale' => $totalWaveSale,
            'totalOmSale' => $totalOmSale,
        ]);
    }
}
