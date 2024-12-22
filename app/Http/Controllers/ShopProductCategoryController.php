<?php

namespace App\Http\Controllers;

use App\Models\ShopProduct;
use Illuminate\Http\Request;
use App\Models\ShopProductCategory;
use App\Models\Product;
class ShopProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        // get shop product categories from station id
        $request->validate([
            'station_id' => 'required|exists:stations,id',
        ]);

        $products = Product::query();

        // join shop products for station id
        $products->join('shop_products', 'products.id', '=', 'shop_products.product_id')
            ->where('shop_products.station_id', $request->station_id);

        // get only distinct categories
        $categories = $products->distinct('category')->get(['category']);

        return $this->jsonResponse($categories->pluck('category'));
    }
}
