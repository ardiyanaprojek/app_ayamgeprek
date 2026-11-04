<?php

use App\Http\Controllers\IngredientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::resource('transactions', TransactionController::class)
    ->only(['index', 'store']);

Route::resource('products', ProductController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::resource('ingredients', IngredientController::class)
    ->only(['index', 'store', 'update', 'destroy']);
