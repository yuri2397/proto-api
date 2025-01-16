<?php

namespace App\Http\Controllers;

use App\Mail\ShopProductProviderCreatedEmail;
use Illuminate\Http\Request;
use App\Models\ShopProductProvider;
use Illuminate\Support\Facades\Mail;

class ShopProductProviderController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'nullable|integer',
            'perPage' => 'nullable|integer',
            'search' => 'nullable|string',
        ]);

        $query = ShopProductProvider::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%')
                ->orWhere('address', 'like', '%' . $request->search . '%');
        }

        $query->orderBy('name', 'asc');

        $shopProductProviders = $query->paginate($request->perPage ?? 10, ['*'], 'page', $request->page ?? 1);

        return $this->jsonResponse($shopProductProviders);
    }

    // show
    public function show(ShopProductProvider $shopProductProvider)
    {   
        return $this->jsonResponse($shopProductProvider);
    }

    // store
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'email' => 'nullable|string',
            'ninea' => 'nullable|string',
            'rccm' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'contact_person_phone' => 'nullable|string',
            'contact_person_email' => 'nullable|string',
            'status' => 'required|in:' . implode(',', ShopProductProvider::STATUS_LIST),
        ]);

        $shopProductProvider = ShopProductProvider::create($request->all());

        // send email to the provider
        Mail::to($shopProductProvider->email)->send(new ShopProductProviderCreatedEmail($shopProductProvider));

        return $this->jsonResponse($shopProductProvider);
    }
}
