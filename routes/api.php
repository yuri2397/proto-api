<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\PumpOperatorController;
use App\Http\Controllers\ShopCashRegisterController;
use App\Http\Controllers\ShopOrderController;
use App\Http\Controllers\ShopProductCategoryController;
use App\Http\Controllers\ShopProductController;
use App\Http\Controllers\ShopProductProviderController;
use App\Http\Controllers\ShopProductSectionController;
use App\Http\Controllers\ShopSaleController;
use App\Http\Controllers\StationCashRegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StationController;

Route::prefix('users')->group(function () {

    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('request-otp', [UserController::class, 'requestOTP']);
    Route::post('validate-otp', [UserController::class, 'validateOTP']);
    Route::post('update-password', [UserController::class, 'updatePassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [UserController::class, 'logout']);
        Route::get('profile', [UserController::class, 'profile']);
    });
});

// shop cash register
Route::prefix('shop-cash-registers')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ShopCashRegisterController::class, 'index']);
    Route::post('open', [ShopCashRegisterController::class, 'openCashRegister']);
    Route::post('close', [ShopCashRegisterController::class, 'closeCashRegister']);
    Route::get('current', [ShopCashRegisterController::class, 'currentOpenCashRegister']);
    Route::get('current-cash-register-details/{cashRegisterId}', [ShopCashRegisterController::class, 'currentCashRegisterDetails']);
    Route::get('dashboard', [ShopCashRegisterController::class, 'dashboard']);
    // courbe d'evolution
    Route::get('sales-evolutions', [ShopCashRegisterController::class, 'salesEvolutions']);
});

// shop sales
Route::prefix('shop-sales')->middleware('auth:sanctum')->group(function () {
    Route::get('/{shopSale}', [ShopSaleController::class, 'show']);
    Route::get('/', [ShopSaleController::class, 'index']);
    Route::post('/', [ShopSaleController::class, 'store']);

});



Route::prefix('stations')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [StationController::class, 'store']);
    Route::get('/', [StationController::class, 'index']);
    Route::get('my-station', [StationController::class, 'getMyStation']);
    Route::get('{station}/report-dashboard', [StationController::class, 'reportDashboard']);
    Route::get('/{station}', [StationController::class, 'show']);
    Route::put('{station}', [StationController::class, 'update']);
    Route::delete('{station}', [StationController::class, 'destroy']);


    Route::post('{station}/open-cash-register', [StationController::class, 'openCashRegister']);
    Route::post('{station}/close-cash-register', [StationController::class, 'closeCashRegister']);
    Route::get('{station}/opened-cash-register', [StationController::class, 'openedCashRegister']);
});

Route::prefix('products')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [ProductController::class, 'store']);
    Route::post('/many', [ProductController::class, 'storeMany'])->withoutMiddleware('auth:sanctum');
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);
    Route::put('/{product}', [ProductController::class, 'update']);
    Route::delete('/{product}', [ProductController::class, 'destroy']);
});

Route::prefix('shop-orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/{shopOrder}/download-pdf', [ShopOrderController::class, 'downloadPdf']);
    Route::get('/', [ShopOrderController::class, 'index']);
    Route::post('/', [ShopOrderController::class, 'store']);
    Route::get('/{shopOrder}', [ShopOrderController::class, 'show']);
    Route::put('/{shopOrder}', [ShopOrderController::class, 'update']);
    Route::delete('/{shopOrder}', [ShopOrderController::class, 'destroy']);

});

Route::prefix('shop-products')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [ShopProductController::class, 'store']);
    Route::post('/many', [ShopProductController::class, 'storeMany']);
    Route::get('/', [ShopProductController::class, 'index']);
    Route::get('/stats', [ShopProductController::class, 'stats']);
    Route::get('/{shopProduct}/flows', [ShopProductController::class, 'shopProductFlows']);
    Route::get('/{shopProduct}', [ShopProductController::class, 'show']);
    Route::put('/{shopProduct}', [ShopProductController::class, 'update']);
    Route::delete('/{shopProduct}', [ShopProductController::class, 'destroy']);


});

// shop-product-providers/
Route::prefix('shop-product-providers')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ShopProductProviderController::class, 'index']);
    Route::get('/{shopProductProvider}', [ShopProductProviderController::class, 'show']);
    Route::post('/', [ShopProductProviderController::class, 'store']);
    Route::put('/{shopProductProvider}', [ShopProductProviderController::class, 'update']);
    Route::delete('/{shopProductProvider}', [ShopProductProviderController::class, 'destroy']);
});

//shop-product-sections
Route::prefix('shop-product-sections')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ShopProductSectionController::class, 'index']);
});

// shop-products-categories
Route::prefix('shop-products-categories')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ShopProductCategoryController::class, 'index']);
});

Route::prefix('pump-operators')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [PumpOperatorController::class, 'store']);
    Route::get('/', [PumpOperatorController::class, 'index']);
    Route::get('/{station}', [PumpOperatorController::class, 'show']);
    Route::put('{station}', [PumpOperatorController::class, 'update']);
    Route::delete('{station}', [PumpOperatorController::class, 'destroy']);
});

Route::prefix('tanks')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [App\Http\Controllers\TankController::class, 'store']);
    Route::get('/', [App\Http\Controllers\TankController::class, 'index']);
    Route::post('/{tank}/add-new-pump', [App\Http\Controllers\TankController::class, 'addNewPump']);
    Route::get('/{tank}', [App\Http\Controllers\TankController::class, 'show']);
    Route::put('{tank}', [App\Http\Controllers\TankController::class, 'update']);
    Route::delete('{tank}', [App\Http\Controllers\TankController::class, 'destroy']);
});

// dashboard
Route::prefix('dashboard')->middleware('auth:sanctum')->group(function () {
    Route::get('/admin-dashboard', [App\Http\Controllers\DashboardController::class, 'adminDashboard']);
});


// orders
Route::prefix('fuel-truck-configs')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Http\Controllers\FuelTruckConfigController::class, 'index']);
    Route::get('/{fuelTruckConfig}/download-pdf', [App\Http\Controllers\FuelTruckConfigController::class, 'downloadPdf']);
    Route::put('/{fuelTruckConfigPart}/update-quantities', [App\Http\Controllers\FuelTruckConfigController::class, 'updatePartQuantities']);

    Route::get('/{fuelTruckConfig}', [App\Http\Controllers\FuelTruckConfigController::class, 'show']);
    Route::post('/', [App\Http\Controllers\FuelTruckConfigController::class, 'store']);
    Route::put('/{fuelTruckConfig}', [App\Http\Controllers\FuelTruckConfigController::class, 'update']);
});

Route::prefix('fuel-truck-config-parts')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Http\Controllers\FuelTruckConfigPartsController::class, 'index']);
    Route::put('/{fuelTruckConfigPart}', [App\Http\Controllers\FuelTruckConfigPartsController::class, 'update']);
    Route::get('/{fuelTruckConfigPart}', [App\Http\Controllers\FuelTruckConfigPartsController::class, 'show']);
    Route::delete('/{fuelTruckConfigPart}', [App\Http\Controllers\FuelTruckConfigPartsController::class, 'destroy']);
});

Route::prefix('tank-stock-flows')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Http\Controllers\TankStockFlowController::class, 'index']);
    Route::get('/{tankStockFlow}', [App\Http\Controllers\TankStockFlowController::class, 'show']);
});

Route::prefix('station-cash-registers')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Http\Controllers\StationCashRegisterController::class, 'index']);
});

Route::prefix('transactions')->middleware('auth:sanctum')->group(function () {});
