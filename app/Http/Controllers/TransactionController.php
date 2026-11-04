<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
                $item['product']->ingredient?->decrement('stok_saat_ini', $item['product']->jumlah_potong * $item['qty']);
            }
        }

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil diselesaikan.');
    }
}
