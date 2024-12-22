<?php

namespace App\Http\Controllers;

use App\Models\ShopProductSection;
use Illuminate\Http\Request;

class ShopProductSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'station_id' => 'required|exists:stations,id',
            'with' => 'nullable|array',
        ]);

        $shopProductSections = ShopProductSection::with($request->with ?? [])
            ->where('station_id', $request->station_id);

        if ($request->search) {
            $shopProductSections->where('name', 'like', '%' . $request->search . '%');
        }

        $paginated = $shopProductSections->paginate($request->perPage ?? 10);

        return response()->json($paginated);
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
    public function show(ShopProductSection $shopProductSection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopProductSection $shopProductSection)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopProductSection $shopProductSection)
    {
        //
    }
}
