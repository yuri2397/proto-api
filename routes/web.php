<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/verify-account/{id}/{hash}', [\App\Http\Controllers\UserController::class, 'verifyEmail'])->name('accounts.verify');
