<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\OperationalClosing;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperationalClosingController extends Controller
{
    public function index(): View
    {
        $closingDate = now()->toDateString();
        $summary = Transaction::query()
            ->whereDate('created_at', $closingDate)
            ->selectRaw('COUNT(*) as transaction_count, COALESCE(SUM(total_bayar), 0) as total_sales')
            ->first();

        return view('closings.index', [
            'closingDate' => $closingDate,
            'summary' => $summary,
            'ingredients' => Ingredient::query()->orderBy('nama_bahan')->get(),
            'todayClosing' => OperationalClosing::query()->whereDate('closing_date', $closingDate)->first(),
            'closings' => OperationalClosing::query()->orderByDesc('closing_date')->limit(30)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cash_counted' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $closingDate = now()->toDateString();

        if (OperationalClosing::query()->whereDate('closing_date', $closingDate)->exists()) {
            return redirect()
                ->route('closings.index')
                ->with('error', 'Operasional hari ini sudah ditutup.');
        }

        $summary = Transaction::query()
            ->whereDate('created_at', $closingDate)
            ->selectRaw('COUNT(*) as transaction_count, COALESCE(SUM(total_bayar), 0) as total_sales')
            ->first();

        $totalSales = (float) $summary->total_sales;
        $stockSnapshot = Ingredient::query()
            ->orderBy('nama_bahan')
            ->get(['nama_bahan', 'satuan', 'stok_saat_ini'])
            ->map(fn (Ingredient $ingredient): array => [
                'name' => $ingredient->nama_bahan,
                'unit' => $ingredient->satuan,
                'quantity' => $ingredient->stok_saat_ini,
            ])
            ->all();

        OperationalClosing::create([
            'closing_date' => $closingDate,
            'total_sales' => $totalSales,
            'transaction_count' => $summary->transaction_count,
            'cash_counted' => $validated['cash_counted'],
            'cash_difference' => (float) $validated['cash_counted'] - $totalSales,
            'stock_snapshot' => $stockSnapshot,
            'notes' => $validated['notes'] ?? null,
            'closed_at' => now(),
        ]);

        return redirect()
            ->route('closings.index')
            ->with('success', 'Tutup operasional berhasil disimpan.');
    }
}
