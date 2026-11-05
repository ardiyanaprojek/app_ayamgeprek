<?php

use App\Http\Controllers\IngredientController;
use App\Http\Controllers\OperationalClosingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::resource('stocks', StockController::class)
    ->only(['index', 'store']);
Route::get('stock-history', [StockController::class, 'history'])
    ->name('stocks.history');

Route::resource('transactions', TransactionController::class)
    ->only(['index', 'store']);
Route::get('transaction-history', [TransactionController::class, 'history'])
    ->name('transaction-history.index');

Route::get('reports/transactions', [ReportController::class, 'transactions'])
    ->name('reports.transactions');

Route::get('closings', [OperationalClosingController::class, 'index'])
    ->name('closings.index');
Route::post('closings', [OperationalClosingController::class, 'store'])
    ->name('closings.store');

Route::resource('products', ProductController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::resource('ingredients', IngredientController::class)
    ->only(['index', 'store', 'update', 'destroy']);
