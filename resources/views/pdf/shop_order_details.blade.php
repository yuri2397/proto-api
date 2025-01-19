@extends('pdf.base-pdf')

@section('header')
    <div class='bg-gray-100 rounded-lg p-2 w-full text-center'>
        <span class='text-gray-500 font-bold text-lg text-center text-dark'> Détails de la commande</span>
    </div>
@endsection

@section('content')
    <br>
    <div class='border-l-2 border-gray-300 pl-4'>
        <p class="text-sm text-gray-500"><b>Fournisseur:</b>
            <span class="text-dark font-bold">{{ $shopOrder->shopProductProvider->name }}</span>
        </p>
        <p class="text-sm text-gray-500"><b>Téléphone:</b> {{ $shopOrder->shopProductProvider->phone }}
        </p>
        <p class="text-sm text-gray-500"><b>Adresse:</b> {{ $shopOrder->shopProductProvider->address }}
        </p>
    </div>
    <br>
    <div class="border-l-2 border-gray-100 pl-4">
        <div class="flex gap-2 flex-col">
            <p class="text-sm text-gray-500"><b>API Market:</b> {{ $shopOrder->station->name }}</p>
            <p class="text-sm text-gray-500"><b>Caissier/Rayonniste:</b> {{ $shopOrder->user->name }}</p>
            <p class="text-sm text-gray-500"><b>N° commande:</b> {{ $shopOrder->order_number }}</p>
            <p class="text-sm text-gray-500"><b>Caissier:</b> {{ $shopOrder->user->name }}</p>
            <p class="text-sm text-gray-500"><b>Date:</b> {{ $shopOrder->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>
    <br>

    <h5 class=" my-4 text-gray-500">Liste des produits</h5>
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 ">
        <thead class="text-xs text-gray-800 uppercase bg-gray-50">
            <tr>
                <th scope="col" class="text-xs p-3">Produit</th>
                <th scope="col" class="text-xs p-3">Quantité</th>
                <th scope="col" class="text-xs p-3">Prix unitaire</th>
                <th scope="col" class="text-xs p-3">Prix total</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($shopOrder->shopOrderItems as $shopOrderItem)
                <tr
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <td scope="row" class="text-xs p-2">{{ $shopOrderItem->shopProductItem->name }}</td>
                    <td class="text-xs p-2">{{ $shopOrderItem->quantity }}</td>
                    <td class="text-xs p-2">{{ format_currency($shopOrderItem->buying_price) }}</td>
                    <td class="text-xs p-2">{{ format_currency($shopOrderItem->totalBuyingPrice) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <div class="flex items-center justify-between bg-gray-100 rounded-lg p-2">
        <h3 class="text-md font-700 text-dark text-lg font-bold">TOTAL COMMANDE:</h3>
        <h3 class="text-md font-700 text-dark text-lg font-bold">{{ format_currency($shopOrder->totalBuyingPrice) }}</h3>
    </div>
    
@endsection
