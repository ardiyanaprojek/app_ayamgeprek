<?php

use App\Http\Controllers\IngredientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::resource('ingredients', IngredientController::class)
    ->only(['index', 'store', 'update', 'destroy']);
