<?php

namespace App\Http\Controllers;

use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopProduct;
use App\Models\ShopProductFlow;
use App\Models\ShopProductItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'with' => 'nullable|array',
            'with.*' => 'nullable|string',
            'perPage' => 'nullable|integer',
            'page' => 'nullable|integer',
            'search' => 'nullable|string',
            'stationId' => 'nullable|integer',
            'userId' => 'nullable|integer',
            'shopProductProviderId' => 'nullable|integer',
        ]);
        $shopOrders = ShopOrder::with($request->with ?? []);

        // station id 
        if ($request->stationId) {
            $shopOrders->where('station_id', $request->stationId);
        }

        // user id 
        if ($request->userId) {
            $shopOrders->where('user_id', $request->userId);
        }

        // shop product provider id
        if ($request->shopProductProviderId) {
            $shopOrders->where('shop_product_provider_id', $request->shopProductProviderId);
        }

        // search 
        if ($request->search) {
            $shopOrders->where('reference', 'like', '%' . $request->search . '%');
        }

        // order by date
        $shopOrders->orderBy('created_at', 'desc');

        // paginated
        $paginated = $this->paginate($shopOrders, $request->perPage ?? 10, $request->page ?? 1);

        return $this->jsonResponse($paginated);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'shop_product_provider_id' => 'required|exists:shop_product_providers,id',
            'date' => 'required|date:Y-m-d H:i:s',
            'items' => 'required|array',
            'items.*.shop_product_id' => 'required|exists:shop_products,id',
            'items.*.quantity' => 'required|numeric',
            'items.*.buying_price' => 'required|numeric',
            'items.*.selling_price' => 'required|numeric',
            'items.*.tva' => 'required|numeric',
        ]);
        DB::beginTransaction();
        try {
            $request->merge(['user_id' => auth()->user()->id, 'station_id' => auth()->user()->owner_id]);

            $request->merge(['status' => ShopOrder::STATUS_ACTIVE]);
            $shopOrder = ShopOrder::create($request->only(
                'order_number',
                'shop_product_provider_id',
                'user_id',
                'station_id',
                'status',
                'date',
            ));

            foreach ($request->items as $item) {
                $shopProduct = ShopProduct::find($item['shop_product_id']);
                if (!$shopProduct) {
                    throw new \Exception('Shop product not found');
                }

                ShopProductFlow::create([
                    'type' => ShopProductFlow::TYPE_ORDER,
                    'quantity' => $item['quantity'],
                    'quantity_before' => $shopProduct->quantity,
                    'quantity_after' => $shopProduct->quantity + $item['quantity'],
                    'shop_product_id' => $shopProduct->id,
                    'user_id' => auth()->user()->id,
                    'data' => [
                        'order_id' => $shopOrder->id,
                        'date' => now()->format('Y-m-d H:i:s'),
                    ],
                ]);

                $shopProductItem = ShopProductItem::create([
                    'shop_product_id' => $shopProduct->id,
                    'quantity' => $item['quantity'],
                    'ean13' => $shopProduct->ean13,
                    'name' => $shopProduct->name,
                    'status' => ShopProductItem::STATUS_ACTIVE,
                    'expiration_date' => $item['expiration_date'] ?? now()->addYear(),
                    'buying_price' => $item['buying_price'],
                    'selling_price' => $item['selling_price'],
                    'tva' => $item['tva'],
                ]);

                ShopOrderItem::create([
                    'shop_order_id' => $shopOrder->id,
                    'shop_product_id' => $shopProduct->id,
                    'shop_product_item_id' => $shopProductItem->id,
                    'quantity' => $item['quantity'],
                    'buying_price' => $item['buying_price'],
                    'selling_price' => $item['selling_price'],
                    'tva' => $item['tva'],
                ]);
            }
            DB::commit();
            $shopOrder->load('shopOrderItems');

            return $this->jsonResponse($shopOrder);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->jsonResponse([
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
                'trace' => $th->getTrace(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ShopOrder $shopOrder)
    {
        $shopOrder->load('shopOrderItems.shopProductItem', 'user', 'shopProductProvider', 'station', 'shopOrderInvoice');
        return $this->jsonResponse($shopOrder);
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

    // download pdf
    public function downloadPdf(ShopOrder $shopOrder)
    {
        $shopOrder->load('shopOrderItems.shopProductItem', 'user', 'shopProductProvider', 'station', 'shopOrderInvoice');
        return view('pdf.shop_order_details', ['shopOrder' => $shopOrder]);
    }

    // download bill
    public function downloadBill(ShopOrder $shopOrder)
    {
        return view('pdf.shop_order_bill', ['shopOrder' => $shopOrder]);
    }
}
