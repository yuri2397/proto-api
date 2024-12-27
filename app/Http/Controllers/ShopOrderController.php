<?php

namespace App\Http\Controllers;

use App\Models\ShopOrder;
use Illuminate\Http\Request;

class ShopOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $shopOrders = ShopOrder::with($request->with ?? []);

        // station id 
        if ($request->station_id) {
            $shopOrders->where('station_id', $request->station_id);
        }

        // user id 
        if ($request->user_id) {
            $shopOrders->where('user_id', $request->user_id);
        }

        // search 
        if ($request->search) {
            $shopOrders->where('reference', 'like', '%' . $request->search . '%');
        }

        // paginated
        $paginated = $this->paginate($shopOrders, $request->per_page ?? 10, $request->page ?? 1);

        return $this->jsonResponse($paginated);
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
    public function show(ShopOrder $shopOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopOrder $shopOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopOrder $shopOrder)
    {
        //
    }
}
