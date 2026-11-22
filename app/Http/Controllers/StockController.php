<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(): View
    {
        $ingredients = Ingredient::query()
            ->orderBy('nama_bahan')
            ->get();

        return view('stocks.index', [
            'ingredients' => $ingredients,
        ]);
    }

    public function history(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
            'tipe' => ['nullable', Rule::in(['masuk', 'rusak_basi', 'penjualan'])],
        ]);

        if (isset($validated['from'], $validated['to'])) {
            $request->validate(['to' => ['after_or_equal:from']]);
        }

        $logs = StockLog::query()
            ->with('ingredient')
            ->when($validated['from'] ?? null, fn ($query, $from) => $query->whereDate('tanggal', '>=', $from))
            ->when($validated['to'] ?? null, fn ($query, $to) => $query->whereDate('tanggal', '<=', $to))
            ->when($validated['tipe'] ?? null, fn ($query, $type) => $query->where('tipe', $type))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('stocks.history', [
            'logs' => $logs,
            'filters' => $validated,
        ]);
    }

    public function pdf(Request $request): Response
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
            'tipe' => ['nullable', Rule::in(['masuk', 'rusak_basi', 'penjualan'])],
        ]);

        if (isset($validated['from'], $validated['to'])) {
            $request->validate(['to' => ['after_or_equal:from']]);
        }

        $logs = StockLog::query()
            ->with('ingredient')
            ->when($validated['from'] ?? null, fn ($query, $from) => $query->whereDate('tanggal', '>=', $from))
            ->when($validated['to'] ?? null, fn ($query, $to) => $query->whereDate('tanggal', '<=', $to))
            ->when($validated['tipe'] ?? null, fn ($query, $type) => $query->where('tipe', $type))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        $pdf = Pdf::loadView('stocks.history-pdf', [
            'logs' => $logs,
            'filters' => $validated,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('riwayat-stok-'.now()->format('Y-m-d-His').'.pdf');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'tipe' => ['required', Rule::in(['masuk', 'rusak_basi'])],
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
