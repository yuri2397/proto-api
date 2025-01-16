<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ShopProduct;
use App\Models\ShopProductFlow;
use App\Models\ShopProductItem;
use App\Models\ShopProductSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShopProductController extends Controller
{
    // stats total active, inactive, rupture de stock
    public function stats(Request $request)
    {
        $request->validate([
            'station_id' => 'required|exists:stations,id',
        ]);

        $expiredProducts = ShopProductItem::where('expiration_date', '<', now())
            ->whereHas('shopProduct', function ($query) use ($request) {
                $query->where('station_id', $request->station_id);
            })
            ->sum('quantity');

        $totalActive = ShopProduct::where('status', 'active')
            ->where('station_id', $request->station_id)->count();

        $totalInactive = ShopProduct::where('status', 'inactive')
            ->where('station_id', $request->station_id)->count();

        $totalRupture = ShopProduct::whereHas('shopProductItems', function ($query) {
            $query->where('quantity', '<=', 0);
        })->count();

        return $this->jsonResponse(['totalActive' => $totalActive, 'totalInactive' => $totalInactive, 'totalRupture' => $totalRupture, 'expiredProducts' => $expiredProducts]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $shopProducts = ShopProduct::with($request->with ?? [])->whereStatus('active');

        // search 
        if ($request->has('search')) {
            $shopProducts->search($request->search);
        }

        if ($request->has('station_id')) {
            $shopProducts->whereStationId($request->station_id);
        }

        $userOwner = Auth::user()->owner;
        // get for only the owner
        $shopProducts->whereStationId($userOwner->id);

        if ($request->has('product_id')) {
            $shopProducts->whereProductId($request->product_id);
        }

        if ($request->has('status')) {
            $shopProducts->whereStatus($request->status);
        }

        // order by name
        $shopProducts->orderBy('name');
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
            if (is_numeric($request->productId)) {
                $product = Product::find($request->productId);
                if (!$product) {
                    return $this->jsonResponse(['message' => 'Product not found'], 404);
                }
            } else {
                $product = Product::create([
                    'name' => $request->productId,
                    'status' => 'active',
                    'ean13' => null,
                    'category' => $request->category,
                    'description' => null,
                    'default_price' => null,
                ]);
            }

            $userOwner = Auth::user()->owner;

            $shopProduct = ShopProduct::create(
                [
                    'name' => $product->name,
                    'status' => 'active',
                    'ean13' => $product->ean13,
                    'default_selling_price' => $request->sellingPrice,
                    'default_buying_price' => $request->buyingPrice,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'shop_product_section_id' => $request->productSectionId,
                    'station_id' => $userOwner->id,
                ]
            );

            // create item 
            $item = ShopProductItem::create([
                'name' => $product->name,
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

    // store many shop products
    public function storeMany(Request $request)
    {

        DB::beginTransaction();
        try {
            $products = Product::all();
            foreach ($products as $product) {
                $userOwner = Auth::user()->owner;
                $shop_product_section = ShopProductSection::where('name', $product->category)->first();
                if (!$shop_product_section) {
                    $shop_product_section = ShopProductSection::create(['name' => $product->category, 'station_id' => $userOwner->id]);
                }

                $shopProduct = ShopProduct::create(
                    [
                        'name' => $product->name,
                        'status' => 'active',
                        'ean13' => $product->ean13,
                        'default_selling_price' => $product->default_price,
                        'default_buying_price' => $product->default_price,
                        'product_id' => $product->id,
                        'quantity' => random_int(1, 100),
                        'shop_product_section_id' => $shop_product_section->id,
                        'station_id' => $userOwner->id,
                    ]
                );

                // create item 
                $item = ShopProductItem::create([
                    'name' => $product->name,
                    'selling_price' => $product->default_price,
                    'buying_price' => $product->default_price,
                    'quantity' => random_int(1, 100),
                    'shop_product_id' => $shopProduct->id,
                ]);

                // create flow
                ShopProductFlow::create([
                    'type' => ShopProductFlow::TYPE_STOCK_IN,
                    'quantity' => $item->quantity,
                    'quantity_before' => 0,
                    'quantity_after' => $item->quantity,
                    'shop_product_id' => $shopProduct->id,
                    'shop_product_item_id' => $item->id,
                    'user_id' => auth()->user()->id,
                ]);
            }
            DB::commit();
            return $this->jsonResponse($products);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->jsonResponse(['message' => $th->getMessage(), 'errors' => $th->getTrace()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ShopProduct $shopProduct, Request $request)
    {
        $shopProduct->load($request->with ?? []);
        return $this->jsonResponse($shopProduct);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopProduct $shopProduct)
    {
        $request->validate([
            'defaultSellingPrice' => 'required|numeric',
            'defaultBuyingPrice' => 'required|numeric',
            'shopProductSectionId' => 'required',
            'category' => 'required|string|max:255',
            'productId' => 'required',
            'status' => 'required|string|in:active,inactive',
        ]);

        // if productId is a string then we search for the product in the database and if it doesn't exist we create it
        if (is_string($request->productId)) {
            $product = Product::where('name', $request->productId)->first();
            if (!$product) {
                $product = Product::create(['name' => $request->productId, 'status' => 'active', 'category' => $request->category]);
            }
        } else {
            $product = Product::find($request->productId);
        }

        // if shopProductSectionId is a string then we search for the product section in the database and if it doesn't exist we create it
        if (is_string($request->shopProductSectionId)) {
            $productSection = ShopProductSection::where('name', $request->shopProductSectionId)->first();
            if (!$productSection) {
                $productSection = ShopProductSection::create(['name' => $request->shopProductSectionId]);
            }
        } else {
            $productSection = ShopProductSection::find($request->shopProductSectionId);
        }

        $shopProduct->update([
            'default_selling_price' => $request->defaultSellingPrice,
            'default_buying_price' => $request->defaultBuyingPrice,
            'shop_product_section_id' => $productSection->id,
            'product_id' => $product->id,
            'status' => $request->status,
        ]);

        // if status change, update shopProductItem status
        $shopProduct->shopProductItems()->update(['status' => $request->status]);

        return $this->jsonResponse($shopProduct);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopProduct $shopProduct)
    {
        //
    }

    // shopProductFlows
    public function shopProductFlows(ShopProduct $shopProduct, Request $request)
    {
        $request->validate([
            'perPage' => 'required|numeric',
            'page' => 'required|numeric',
            'with' => 'nullable|array',
            'search' => 'nullable|string',
        ]);

        $query = ShopProductFlow::with($request->with ?? [])->where('shop_product_id', $shopProduct->id);

        if ($request->has('search')) {
            $query->search($request->search);
        }

        // type 
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // order by created_at
        $query->orderBy('created_at', 'desc');

        $paginated = $this->paginate($query, $request->perPage, $request->page, $request->columns ?? ['*']);
        return $this->jsonResponse($paginated);
    }
}
