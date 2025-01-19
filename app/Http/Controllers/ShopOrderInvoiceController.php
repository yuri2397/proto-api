<?php

namespace App\Http\Controllers;

use App\Mail\ShopOrderInvoiceCreatedMail;
use App\Models\ShopOrder;
use App\Models\ShopOrderInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ShopOrderInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'perPage' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
            'shopProductProviderId' => 'nullable|exists:shop_product_providers,id',
            'status' => 'nullable|string|in:' . implode(',', ShopOrderInvoice::STATUS_LIST),
        ]);
        $query = ShopOrderInvoice::query();

       
        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        if ($request->has('shopProductProviderId')) {
            $query->where('shop_product_provider_id', $request->shopProductProviderId);
        }

        $query->orderBy('created_at', 'desc');

        $paginated = $query->paginate($request->perPage ?? 10, ['*'], 'page', $request->page ?? 1);
        return response()->json($paginated);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'toDate' => 'required|date:Y-m-d',
            'shopProductProviderId' => 'required|exists:shop_product_providers,id',
        ]);

        DB::beginTransaction();
        try {
            // all order when date is <= to_date and shop_product_provider_id is the same as the one in the request and status is active and have not invoice
            $orders = ShopOrder::where('date', '<=', $request->toDate)
                ->where('shop_product_provider_id', $request->shopProductProviderId)
                ->where('status', ShopOrder::STATUS_ACTIVE)
                ->where('shop_order_invoice_id', null)
                ->get();

            if ($orders->isEmpty()) {
                return response()->json(['message' => 'Toutes les commandes ont déjà une facture'], 422);
            }

            $shopOrderInvoice = ShopOrderInvoice::create([
                'date' => $request->toDate,
                'shop_product_provider_id' => $request->shopProductProviderId,
                'user_id' => auth()->user()->id,
                'total_amount' => 0,
                'status' => ShopOrderInvoice::STATUS_UNPAID,
            ]);

            $totalAmount = 0;
            foreach ($orders as $order) {
                $totalAmount += $order->totalBuyingPrice;
                $order->shop_order_invoice_id = $shopOrderInvoice->id;
                $order->save();
            }

            $shopOrderInvoice->total_amount = $totalAmount;
            $shopOrderInvoice->save();

            // generate pdf
            // $pdf = PDF::loadView('pdf.shop_order_invoice', ['shopOrderInvoice' => $shopOrderInvoice]);
            // send mail to the provider
            // Mail::to($shopOrderInvoice->shopProductProvider->email)->send(new ShopOrderInvoiceCreatedMail($shopOrderInvoice, $pdf));

            DB::commit();
            return response()->json($shopOrderInvoice);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'error' => $th], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ShopOrderInvoice $shopOrderInvoices)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopOrderInvoice $shopOrderInvoices)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopOrderInvoice $shopOrderInvoices)
    {
        //
    }

    // download pdf
    public function downloadPdf(ShopOrderInvoice $shopOrderInvoice)
    {
        $shopOrderInvoice = $shopOrderInvoice->load(['shopProductProvider', 'user', 'shopOrders.station']);
        // return $shopOrderInvoice;
        return view('pdf.shop_order_invoice', ['invoiceData' => $shopOrderInvoice]);
    }
}
