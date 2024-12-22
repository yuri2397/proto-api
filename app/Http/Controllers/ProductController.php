<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::with($request->with ?? []);

        if ($request->has('search')) {
            $products->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status')) {
            $products->where('status', $request->status);
        }

        // paginate
        $paginated = $this->paginate($products, $request->per_page ?? 10, $request->page ?? 1, $request->columns ?? ['*']);
        return $this->jsonResponse($paginated);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:active,inactive',
            'ean13' => 'nullable|string|max:13|unique:products,ean13',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'default_price' => 'nullable|numeric',
        ]);

        $product = Product::create($request->only('name', 'status', 'ean13', 'category', 'description', 'default_price'));
        return $this->jsonResponse($product);
    }

    // store many products
    public function storeMany(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.name' => 'required|string|max:255',
            'products.*.status' => 'required|string|in:active,inactive',
            'products.*.ean13' => 'nullable|string|max:13|unique:products,ean13',
            'products.*.category' => 'nullable|string|max:255',
            'products.*.description' => 'nullable|string|max:255',
            'products.*.default_price' => 'nullable|numeric',
        ]);

        $products = Product::insert($request->products);
        return $this->jsonResponse($products);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product, Request $request)
    {
        $product->load($request->with ?? []);
        return $this->jsonResponse($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'string|max:255',
            'status' => 'string|in:active,inactive',
            'ean13' => 'string|max:13|unique:products,ean13,' . $product->id,
            'category' => 'string|max:255',
            'description' => 'string|max:255',
            'default_price' => 'numeric',
        ]);

        // update if value in request is not null
        $product->update($request->only('name', 'status', 'ean13', 'category', 'description', 'default_price'));
        return $this->jsonResponse($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->update(['status' => 'inactive']);
        return $this->jsonResponse($product);
    }
}
