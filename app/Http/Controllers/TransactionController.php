<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockLog;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with('ingredient')
            ->orderBy('nama_menu')
            ->get();

        return view('transactions.index', [
            'products' => $products,
        ]);
    }

    public function history(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:50'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        if (isset($validated['from'], $validated['to'])) {
            $request->validate(['to' => ['after_or_equal:from']]);
        }

        $transactions = Transaction::query()
            ->with('items.product')
            ->when($validated['q'] ?? null, fn ($query, $search) => $query->where('nomor_struk', 'like', '%'.$search.'%'))
            ->when($validated['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($validated['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('transactions.history', [
            'transactions' => $transactions,
            'filters' => $validated,
        ]);
    }

    public function receipt(Transaction $transaction): View
    {
        $transaction->load('items.product');

        return view('transactions.receipt', [
            'transaction' => $transaction,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.catatan' => ['nullable', 'string', 'max:100'],
            'nominal_diterima' => ['required', 'numeric', 'min:0'],
        ]);

        $items = [];
        $total = 0;

        foreach ($validated['items'] as $entry) {
            $product = Product::with('ingredient')->findOrFail($entry['product_id']);
            $qty = (int) $entry['qty'];
            $subtotal = $product->harga_jual * $qty;
            $total += $subtotal;

            if ($product->ingredient_id && $product->jumlah_potong > 0) {
                $needed = $product->jumlah_potong * $qty;
                $stock = $product->ingredient?->stok_saat_ini ?? 0;

                if ($stock < $needed) {
                    return redirect()
                        ->route('transactions.index')
                        ->with('error', 'Stok bahan tidak mencukupi untuk beberapa menu.');
                }
            }

            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'catatan' => $entry['catatan'] ?? null,
                'harga_satuan' => $product->harga_jual,
                'subtotal' => $subtotal,
            ];
        }

        $nominalDiterima = (float) $validated['nominal_diterima'];

        if ($nominalDiterima < $total) {
            return redirect()
                ->route('transactions.index')
                ->with('error', 'Uang yang diterima kurang dari total pembayaran.');
        }

        $kembalian = $nominalDiterima - $total;
        $nomorStruk = 'NOTA-'.now()->format('YmdHis');

        $transaction = Transaction::create([
            'nomor_struk' => $nomorStruk,
            'total_bayar' => $total,
            'nominal_diterima' => $nominalDiterima,
            'kembalian' => $kembalian,
            'metode_bayar' => 'TUNAI',
        ]);

        foreach ($items as $item) {
            $transaction->items()->create([
                'product_id' => $item['product']->id,
                'qty' => $item['qty'],
                'harga_satuan' => $item['harga_satuan'],
                'subtotal' => $item['subtotal'],
                'catatan' => $item['catatan'],
            ]);

            if ($item['product']->ingredient_id && $item['product']->jumlah_potong > 0) {
                $used = $item['product']->jumlah_potong * $item['qty'];
                $item['product']->ingredient?->decrement('stok_saat_ini', $used);

                StockLog::create([
                    'ingredient_id' => $item['product']->ingredient_id,
                    'tipe' => 'penjualan',
                    'jumlah' => $used,
                    'tanggal' => now()->toDateString(),
                    'keterangan' => 'Penjualan '.$item['product']->nama_menu,
                ]);
            }
        }

        return redirect()
            ->route('transactions.receipt', $transaction)
            ->with('success', 'Transaksi berhasil diselesaikan.');
    }
}
