<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(): View
    {
        $ingredients = Ingredient::query()
            ->orderBy('nama_bahan')
            ->get();

        $stockLogs = StockLog::query()
            ->with('ingredient')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('stocks.index', [
            'ingredients' => $ingredients,
            'stockLogs' => $stockLogs,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'tipe' => ['required', Rule::in(['masuk', 'penjualan', 'rusak_basi'])],
            'jumlah' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $ingredient = Ingredient::findOrFail($validated['ingredient_id']);
        $delta = $validated['tipe'] === 'masuk' ? $validated['jumlah'] : -$validated['jumlah'];
        $ingredient->stok_saat_ini = max(0, $ingredient->stok_saat_ini + $delta);
        $ingredient->save();

        StockLog::create([
            'ingredient_id' => $ingredient->id,
            'tipe' => $validated['tipe'],
            'jumlah' => $validated['jumlah'],
            'tanggal' => now()->toDateString(),
            'keterangan' => $validated['keterangan'] ?? 'Mutasi stok',
        ]);

        return redirect()
            ->route('stocks.index')
            ->with('success', 'Perubahan stok berhasil disimpan.');
    }
}
