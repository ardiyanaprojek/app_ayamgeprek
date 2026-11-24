<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\OperationalClosingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransactionController;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

// rouute untuk pengguna yang belum login
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login');
});

// route untuk pengguna yang sudah login
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/', function () {
        $today = now()->toDateString();
        $weekStart = now()->subDays(6)->startOfDay();
        $dailySales = Transaction::query()
            ->selectRaw('DATE(created_at) as tanggal, SUM(total_bayar) as total')
            ->whereBetween('created_at', [$weekStart, now()])
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $salesChart = collect(range(6, 0))->map(function (int $daysAgo) use ($dailySales) {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->translatedFormat('D'),
                'date' => $date->toDateString(),
                'total' => (float) ($dailySales[$date->toDateString()] ?? 0),
            ];
        });

        return view('dashboard', [
            'todaySales' => Transaction::query()->whereDate('created_at', $today)->sum('total_bayar'),
            'todayTransactions' => Transaction::query()->whereDate('created_at', $today)->count(),
            'productCount' => Product::query()->count(),
            'lowStockIngredients' => Ingredient::query()
                ->whereColumn('stok_saat_ini', '<=', 'stok_minimum')
                ->orderBy('stok_saat_ini')
                ->take(5)
                ->get(),
            'lowStockCount' => Ingredient::query()
                ->whereColumn('stok_saat_ini', '<=', 'stok_minimum')
                ->count(),
            'recentTransactions' => Transaction::query()
                ->withCount('items')
                ->latest()
                ->take(6)
                ->get(),
            'salesChart' => $salesChart,
        ]);
    })->name('dashboard');

    Route::resource('stocks', StockController::class)
        ->only(['index', 'store']);
    Route::get('stock-history', [StockController::class, 'history'])
        ->name('stocks.history');
    Route::get('stock-history/pdf', [StockController::class, 'pdf'])
        ->name('stocks.history.pdf');

    Route::resource('transactions', TransactionController::class)
        ->only(['index', 'store']);
    Route::get('transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])
        ->name('transactions.receipt');
    Route::get('transaction-history', [TransactionController::class, 'history'])
        ->name('transaction-history.index');

    Route::get('reports/transactions', [ReportController::class, 'transactions'])
        ->name('reports.transactions');
    Route::get('reports/transactions/pdf', [ReportController::class, 'pdf'])
        ->name('reports.transactions.pdf');

    Route::get('closings', [OperationalClosingController::class, 'index'])
        ->name('closings.index');
    Route::post('closings', [OperationalClosingController::class, 'store'])
        ->name('closings.store');

    Route::resource('products', ProductController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('ingredients', IngredientController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::get('settings', [SettingsController::class, 'index'])
        ->name('settings.index');
    Route::put('settings/account', [SettingsController::class, 'updateAccount'])
        ->name('settings.account.update');
    Route::put('settings/shop', [SettingsController::class, 'updateShop'])
        ->name('settings.shop.update');
});