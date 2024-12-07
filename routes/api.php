<?php

use App\Http\Controllers\PumpOperatorController;
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


Route::prefix('stations')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [StationController::class, 'store']);
    Route::get('/', [StationController::class, 'index']);
    Route::get('my-station', [StationController::class, 'getMyStation']);
    Route::get('/{station}', [StationController::class, 'show']);
    Route::put('{station}', [StationController::class, 'update']);
    Route::delete('{station}', [StationController::class, 'destroy']);

    Route::post('{station}/open-cash-register', [StationController::class, 'openCashRegister']);
    Route::post('{station}/close-cash-register', [StationController::class, 'closeCashRegister']);
    Route::get('{station}/opened-cash-register', [StationController::class, 'openedCashRegister']);
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

// orders
Route::prefix('station-fuel-orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Http\Controllers\StationFuelOrderController::class, 'index']);
    Route::get('/{stationFuelOrder}', [App\Http\Controllers\StationFuelOrderController::class, 'show']);
    Route::post('/', [App\Http\Controllers\StationFuelOrderController::class, 'store']);
});

Route::prefix('station-cash-registers')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Http\Controllers\StationCashRegisterController::class, 'index']);
});

Route::prefix('transactions')->middleware('auth:sanctum')->group(function () {
    
});
