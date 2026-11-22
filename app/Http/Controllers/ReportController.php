<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function transactions(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        if (isset($validated['from'], $validated['to'])) {
            $request->validate(['to' => ['after_or_equal:from']]);
        }

        $from = $validated['from'] ?? $validated['to'] ?? now()->toDateString();
        $to = $validated['to'] ?? $validated['from'] ?? now()->toDateString();

        $transactions = Transaction::query()
            ->with('items.product')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $summary = Transaction::query()
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->selectRaw('COUNT(*) as transaction_count, COALESCE(SUM(total_bayar), 0) as total_sales')
            ->first();

        return view('reports.transactions', [
            'transactions' => $transactions,
            'from' => $from,
            'to' => $to,
            'summary' => $summary,
        ]);
    }

    public function pdf(Request $request): Response
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        if (isset($validated['from'], $validated['to'])) {
            $request->validate(['to' => ['after_or_equal:from']]);
        }

        $from = $validated['from'] ?? $validated['to'] ?? now()->toDateString();
        $to = $validated['to'] ?? $validated['from'] ?? now()->toDateString();

        $transactions = Transaction::query()
            ->with('items.product')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->orderByDesc('created_at')
            ->get();

        $summary = Transaction::query()
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->selectRaw('COUNT(*) as transaction_count, COALESCE(SUM(total_bayar), 0) as total_sales')
            ->first();

        $pdf = Pdf::loadView('reports.transactions-pdf', [
            'transactions' => $transactions,
            'from' => $from,
            'to' => $to,
            'summary' => $summary,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-transaksi-'.$from.'-'.$to.'.pdf');
    }
}
