@extends('pdf.base-pdf')

@section('header')
    <div class='bg-gray-100 rounded-lg p-2 w-full text-center'>
        <span class='text-gray-500 font-bold text-lg text-center text-dark'>Fiche de réception</span>
    </div>
@endsection

@section('content')
    <br>
    <div class="border-l-2 border-gray-100 pl-4">
        <div class="flex gap-2 flex-col">
            <p class="text-sm text-gray-500">API Market: <b>{{ $shopOrder->station->name }}</b></p>
            <p class="text-sm text-gray-500">Fournisseur: <b>{{ $shopOrder->shopProductProvider->name }}</b></p>
            {{-- CAISSIER / RAYONNISTE: --}}
            <p class="text-sm text-gray-500">Caissier / Rayonniste: <b>{{ $shopOrder->user->name }}</b></p>
            {{-- AGENT SECURITE: ......... --}}
            <p class="text-sm text-gray-500">Agent de sécurité: <b>________________________________________</b></p>
            {{-- N° commande: --}}
            <p class="text-sm text-gray-500">N° commande: <b>{{ $shopOrder->order_number }}</b></p>
            <p class="text-sm text-gray-500">Date: <b>{{ $shopOrder->created_at->format('d/m/Y H:i') }}</b></p>
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
    <div class="flex items-center justify-between bg-gray-100  p-2">
        <h3 class="text-md font-700 text-dark text-lg font-bold">TOTAL FACTURE</h3>
        <h3 class="text-md font-700 text-dark text-lg font-bold">{{ format_currency($shopOrder->totalBuyingPrice) }}</h3>
    </div>
    <br><br>
    <div class="flex flex-col gap-2 border-l-2 border-gray-100 pl-4">
        <h4 class="text-sm text-gray-500">VISA / SIGNATURES :</h4>
        <p class="text-sm text-red-300">NB: Ce document doit être signé par tous les signataires pour être valide.</p>
    </div>
    <br>
    {{-- ajouter 4 cases pour les signatures avec des bordures et le titre du signataire     --}}
    <div class="flex items-center justify-between gap-2">
        <div class="border-2 border-gray-100 p-2 text-center rounded-lg w-1/3">
            <h3 class="text-sm font-500 text-dark text-md font-bold underline">CHEF BOUTIQUE</h3>
            <br>
            <br>
            <h3 class="text-sm font-500 text-gray-100 text-md font-bold">............................</h3>
        </div>
        <div class="border-2 border-gray-100 p-2 text-center rounded-lg w-1/3">
            <h3 class="text-sm font-500 text-dark text-md font-bold underline">CAISSIER</h3>
            <br>
            <br>
            <h3 class="text-sm font-500 text-gray-100 text-md font-bold">............................</h3>
        </div>
        <div class="border-2 border-gray-100 p-2 text-center rounded-lg w-1/3">
            <h3 class="text-sm font-500 text-dark text-md font-bold underline">AGENT SECURITE</h3>
            <br>
            <br>
            <h3 class="text-sm font-500 text-gray-100 text-md font-bold">............................</h3>
        </div>
        <div class="border-2 border-gray-100 p-2 text-center rounded-lg w-1/3">
            <h3 class="text-sm font-500 text-dark text-md font-bold underline">FOURNISSEUR</h3>
            <br>
            <br>
            <h3 class="text-sm font-500 text-gray-100 text-md font-bold">............................</h3>
        </div>
    </div>
@endsection
