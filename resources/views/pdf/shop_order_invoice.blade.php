@extends('pdf.base-pdf')

@section('header')
    <div class='bg-gray-100 rounded-lg p-2 w-full text-center'>
        <span class='text-gray-500 font-bold text-lg text-center text-dark'> Facture fournisseur</span>
    </div>
@endsection

@section('content')
    <div class="flex items-center justify-between align-start mt-4">
        <div class="flex items-center justify-center gap-2 border-l-2 border-gray-100 pl-4">
            <img class="h-auto max-w-full rounded-lg" width="100px" height="100px"
                src="https://api.sn/wp-content/uploads/2023/01/logo-api-1-1024x770.png" alt="">
            <div>
                <p class="text-sm text-gray-500">API Market</p>
                <p class="text-sm text-gray-500">api@market.sn</p>
                <p class="text-sm text-gray-500">+228 97 00 00 00</p>
                <p class="text-sm text-gray-500">123 Rue de la Paix, Dakar, Sénégal</p>
            </div>
        </div>
        <div class='border-l-2 border-gray-100 pl-4'>
            <p class="text-sm text-gray-500">Fournisseur: <b>{{ $invoiceData->shopProductProvider->name }}</b></p>
            <p class="text-sm text-gray-500">Email: <b>{{ $invoiceData->shopProductProvider->email }}</b></p>
            <p class="text-sm text-gray-500">Téléphone: <b>{{ $invoiceData->shopProductProvider->phone }}</b></p>
            <p class="text-sm text-gray-500">Adresse: <b>{{ $invoiceData->shopProductProvider->address }}</b></p>
        </div>
    </div>
    <br>
    <div class="border-l-2 border-gray-100 pl-4">
        <div class="flex gap-2 flex-col">
            {{-- N° facture: --}}
            <p class="text-sm text-gray-500">N° facture: <b>{{ $invoiceData->reference }}</b></p>
            {{-- Date: --}}
            <p class="text-sm text-gray-500">Date: <b>{{ $invoiceData->created_at->format('d/m/Y H:i') }}</b></p>
            {{-- montant total: --}}
            <p class="text-sm text-gray-500">Montant total: <b>{{ format_currency($invoiceData->total_amount) }}</b></p>
            {{-- Statut: --}}
            @if ($invoiceData->status == 'unpaid')
                <p class="text-sm text-gray-500">Statut: <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded">Non payée</span></p>
            @else
                <p class="text-sm text-gray-500">Statut: <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded">Payée</span></p>
            @endif
        </div>
    </div>
    <br>
    <br>
    <h5 class=" my-4 text-gray-500">Commandes facturées</h5>
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 ">
        <thead class="text-xs text-gray-800 uppercase bg-gray-50">
            <tr>
                <th scope="col" class="text-xs p-3">N° commande</th>
                <th scope="col" class="text-xs p-3">Date</th>
                <th scope="col" class="text-xs p-3">Nbrs produits</th>
                <th scope="col" class="text-xs p-3">Boutique</th>
                <th scope="col" class="text-xs p-3">Montant total</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($invoiceData->shopOrders as $shopOrder)
                <tr
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <td scope="row" class="text-xs p-2">{{ $shopOrder->reference }}</td>
                    <td class="text-xs p-2">{{ $shopOrder->created_at->format('d/m/Y') }}</td>
                    <td class="text-xs p-2">{{ $shopOrder->totalProductsItems }}</td>
                    <td class="text-xs p-2">{{ $shopOrder->station->name }}</td>
                    <td class="text-xs p-2">{{ format_currency($shopOrder->totalBuyingPrice) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="flex items-center justify-between bg-gray-100  p-2">
        <h3 class="text-md font-700 text-dark text-lg font-bold">MONTANT TOTAL</h3>
        <h3 class="text-md font-700 text-dark text-lg font-bold">{{ format_currency($invoiceData->total_amount) }}</h3>
    </div>
@endsection
