<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ShopProduct;
use App\Models\ShopProductFlow;
use App\Models\ShopProductItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopProductController extends Controller
{
    // stats total active, inactive, rupture de stock
    public function stats(Request $request)
    {
        $request->validate([
            'station_id' => 'required|exists:stations,id',
        ]);

        $totalActive = ShopProduct::where('status', 'active')
            ->where('station_id', $request->station_id)->count();
        $totalInactive = ShopProduct::where('status', 'inactive')
            ->where('station_id', $request->station_id)->count();
        $totalRupture = ShopProduct::where('quantity', '<=', 0)
            ->where('station_id', $request->station_id)->count();

        return $this->jsonResponse(['totalActive' => $totalActive, 'totalInactive' => $totalInactive, 'totalRupture' => $totalRupture]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $shopProducts = ShopProduct::with($request->with ?? []);

        // search 
        if ($request->has('search')) {
            $shopProducts->search($request->search);
        }

        if ($request->has('station_id')) {
            $shopProducts->whereStationId($request->station_id);
        }

        if ($request->has('product_id')) {
            $shopProducts->whereProductId($request->product_id);
        }

        if ($request->has('status')) {
            $shopProducts->whereStatus($request->status);
        }

        // paginate
        $paginated = $this->paginate($shopProducts, $request->perPage ?? 10, $request->page ?? 1, $request->columns ?? ['*']);
        return $this->jsonResponse($paginated);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sellingPrice' => 'required|numeric',
            'buyingPrice' => 'required|numeric',
            'quantity' => 'required|numeric',
            'productSectionId' => 'required|exists:shop_product_sections,id',
            'category' => 'required|string|max:255',
            'productId' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // if productId is not a number, get the product id from the product name
            if (is_numeric($request->productId)) {
                $product = Product::find($request->productId);
                if (!$product) {
                    return $this->jsonResponse(['message' => 'Product not found'], 404);
                }
                $request->merge(['name' => $product->name]);
            } else {
                $request->merge(['name' => $request->productId]);
                // create product
                $product = Product::create([
                    'name' => $request->productId,
                    'status' => 'active',
                    'ean13' => null,
                    'category' => $request->category,
                    'description' => null,
                    'default_price' => null,
                ]);
                $request->merge(['product_id' => $product->id]);
            }

            $shopProduct = ShopProduct::create($request->only('name', 'status', 'ean13', 'category', 'description', 'default_price', 'product_id'));

            // create item 
            $item = ShopProductItem::create([
                'name' => $request->name,
                'selling_price' => $request->sellingPrice,
                'buying_price' => $request->buyingPrice,
                'quantity' => $request->quantity,
                'shop_product_id' => $shopProduct->id,
            ]);

            // create flow
            ShopProductFlow::create([
                'type' => ShopProductFlow::TYPE_STOCK_IN,
                'quantity' => $request->quantity,
                'quantity_before' => 0,
                'quantity_after' => $request->quantity,
                'shop_product_id' => $shopProduct->id,
                'shop_product_item_id' => $item->id,
                'user_id' => auth()->user()->id,
            ]);
            DB::commit();
            return $this->jsonResponse($shopProduct);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->jsonResponse(['message' => $th->getMessage(), 'errors' => $th->getTrace()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ShopProduct $shopProduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopProduct $shopProduct)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopProduct $shopProduct)
    {
        //
    }
}
