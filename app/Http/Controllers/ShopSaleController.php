<?php

namespace App\Http\Controllers;

use App\Models\ShopCashRegister;
use App\Models\ShopProduct;
use App\Models\ShopProductFlow;
use App\Models\ShopProductItem;
use App\Models\ShopSale;
use App\Models\ShopSaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopSaleController extends Controller
{

    public function index(Request $request)
    {
        $query = ShopSale::with($request->with ?? [])->where('station_id', auth()->user()->owner_id);
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // cashregister id 
        if ($request->has('cashRegisterId')) {
            $query->where('cash_register_id', $request->cashRegisterId);
        }

        $query->orderBy('created_at', 'desc');

        $paginated = $this->paginate($query, $request->perPage, $request->page, $request->columns ?? ['*']);
        return $this->jsonResponse($paginated);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.shopProductId' => 'required|exists:shop_products,id',
            'items.*.proposePrice' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',
            'items.*.sellingPrice' => 'required|numeric',
            'items.*.discount' => 'nullable|numeric',
            'paymentMethod' => 'required|string:in:' . implode(',', ShopSale::PAYMENT_METHOD_LIST),
            'givenAmount' => 'nullable|numeric',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $stationId = auth()->user()->owner_id;

            if (!$stationId) {
                return $this->jsonResponse(['message' => 'Station not found'], 404);
            }

            $cashRegister = ShopCashRegister::where('status', ShopCashRegister::STATUS_OPEN)
                ->where('station_id', $stationId)
                ->first();

            if (!$cashRegister) {
                return $this->jsonResponse(['message' => 'Aucun caisse ouverte trouvée. Veuillez ouvrir un caisse d\'abord.'], 422);
            }

            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['sellingPrice'] * $item['quantity'];
            }

            $globalDiscount = 0;
            foreach ($request->items as $item) {
                $globalDiscount += $item['discount'];
            }

            $totalSaleAmount = $totalAmount - $globalDiscount;

            

            if ($request->paymentMethod == ShopSale::PAYMENT_METHOD_CASH) {
                $returnedAmount = $request->givenAmount - $totalSaleAmount;
                if ($request->givenAmount < $totalSaleAmount) {
                    return $this->jsonResponse(
                        [
                            'message' => 'Le montant donné est inférieur au montant total de la vente.',
                            'totalSaleAmount' => $totalSaleAmount,
                            'givenAmount' => $request->givenAmount,
                        ],
                        422
                    );
                }
            } else {
                $returnedAmount = 0;
            }

            $newSale = ShopSale::create([
                'station_id' => $stationId,
                'cash_register_id' => $cashRegister->id,
                'user_id' => auth()->user()->id,
                'total_amount' => $totalAmount, // total sale amount
                'given_amount' => $request->givenAmount ?? $totalSaleAmount, // customer given amount
                'returned_amount' => $returnedAmount, // returned amount to the customer
                'remarks' => $request->remarks,
                'payment_method' => $request->paymentMethod,
                'status' => ShopSale::STATUS_PAID,
            ]);

            // create sale items
            foreach ($request->items as $item) {
                $shopProduct = ShopProduct::where('id', $item['shopProductId'])
                    ->where('station_id', $stationId)
                    ->where('status', ShopProduct::STATUS_ACTIVE)
                    ->first();

                if (!$shopProduct) {
                    throw new \Exception('Le produit de la boutique n\'est plus disponible ou est inactif');
                }

                ShopProductFlow::create([
                    'type' => ShopProductFlow::TYPE_SALE,
                    'quantity' => $item['quantity'],
                    'quantity_before' => $shopProduct->quantity,
                    'quantity_after' => $shopProduct->quantity - $item['quantity'],
                    'shop_product_id' => $shopProduct->id,
                    'user_id' => auth()->user()->id,
                    'data' => [
                        'sale_id' => $newSale->id,
                        'shop_product_id' => $shopProduct->id,
                        'date' => now()->format('Y-m-d H:i:s'),
                    ],
                ]);

                $quantityToSubtract = $item['quantity'];

                $currentShopProductItems = $shopProduct->shopProductItems
                    ->where('status', ShopProductItem::STATUS_ACTIVE)
                    ->sortBy([
                        ['created_at', 'asc'],  // Puis par date d'entrée (plus ancien en premier)
                        ['quantity', 'asc'],    // Puis par quantité (quantité plus petite en premier)
                    ]);

                foreach ($currentShopProductItems as $currentShopProductItem) {
                    if ($quantityToSubtract <= 0) {
                        break;
                    }

                    if ($quantityToSubtract > $currentShopProductItem->quantity) {
                        $quantityToSubtract -= $currentShopProductItem->quantity;
                        $currentShopProductItem->quantity = 0;
                    } else {
                        $currentShopProductItem->quantity -= $quantityToSubtract;
                        $quantityToSubtract = 0;
                    }

                    $currentShopProductItem->save();
                }

                ShopSaleItem::create([
                    'shop_sale_id' => $newSale->id,
                    'shop_product_id' => $item['shopProductId'],
                    'quantity' => $item['quantity'],
                    'proposer_amount' => $item['proposePrice'],
                    'sold_amount' => $item['sellingPrice'],
                    'discount' => $item['discount'],
                ]);
            }

            DB::commit();
            return $this->jsonResponse(['message' => 'Vente effectuée avec succès', 'sale' => $newSale, 'items' => $request->items, 'returnedAmount' => $returnedAmount, 'totalAmount' => $totalAmount, 'givenAmount' => $request->givenAmount, 'paymentMethod' => $request->paymentMethod], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse(['message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    // show
    public function show($id): JsonResponse
    {
        $sale = ShopSale::with(['shopSaleItems.shopProduct', 'user'])->find($id);
        return $this->jsonResponse($sale);
    }
}
