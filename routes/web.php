<?php

use App\Http\Controllers\IngredientController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::resource('products', ProductController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::resource('ingredients', IngredientController::class)
    ->only(['index', 'store', 'update', 'destroy']);
