<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $transaction->nomor_struk }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #e9ecef;
            color: #111;
            font-family: "Courier New", monospace;
            font-size: 12px;
        }
        .receipt-actions {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding: 12px;
        }
        .receipt-actions button,
        .receipt-actions a {
            border: 1px solid #dc3545;
            border-radius: 4px;
            background: #dc3545;
            color: #fff;
            cursor: pointer;
            font-family: sans-serif;
            padding: 8px 14px;
            text-decoration: none;
        }
        .receipt-actions a {
            background: #fff;
            color: #dc3545;
        }
        .receipt {
            width: 80mm;
            min-height: 120mm;
            margin: 0 auto 20px;
            padding: 5mm 4mm;
            background: #fff;
        }
        .center { text-align: center; }
        .bold { font-weight: 700; }
        .store-name { font-size: 15px; }
        .divider {
            border-top: 1px dashed #555;
            margin: 8px 0;
        }
        .meta {
            display: flex;
            justify-content: space-between;
        }
        .item { margin: 5px 0; }
        .item-name { margin-bottom: 2px; }
        .item-line {
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }
        .totals {
            display: grid;
            gap: 3px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
        }
        .thanks { margin-top: 10px; }
        @media print {
            @page { size: 80mm auto; margin: 0; }
            body { background: #fff; }
            .receipt-actions { display: none; }
            .receipt {
                width: 80mm;
                min-height: 0;
                margin: 0;
                padding: 5mm 4mm;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-actions">
        <button type="button" onclick="window.print()">Cetak Struk</button>
        <a href="{{ route('transactions.index') }}">Transaksi Baru</a>
        <a href="{{ route('transaction-history.index') }}">Riwayat</a>
    </div>

    <main class="receipt">
        <div class="center store-name bold">AYAM GEPREK ISTIQOMAH</div>
        <div class="center">PADAKASIH</div>
        <div class="divider"></div>

        <div class="meta">
            <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            <span>{{ $transaction->nomor_struk }}</span>
        </div>
        <div>Kasir: {{ auth()->user()->name ?? '-' }}</div>
        <div class="divider"></div>

        @foreach ($transaction->items as $item)
            <div class="item">
                <div class="item-name">{{ $item->product?->nama_menu ?? 'Menu dihapus' }}</div>
                <div class="item-line">
                    <span>{{ $item->qty }} x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
                    <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @if ($item->catatan)
                    <div>Catatan: {{ $item->catatan }}</div>
                @endif
            </div>
        @endforeach

        <div class="divider"></div>
        <div class="totals">
            <div class="totals-row bold">
                <span>TOTAL</span>
                <span>Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</span>
            </div>
            <div class="totals-row">
                <span>TUNAI</span>
                <span>Rp {{ number_format($transaction->nominal_diterima, 0, ',', '.') }}</span>
            </div>
            <div class="totals-row">
                <span>Kembalian</span>
                <span>Rp {{ number_format($transaction->kembalian, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="divider"></div>
        <div class="center thanks">Terima kasih</div>
    </main>

    @if (session('success'))
        <script>
            window.addEventListener('load', () => {
                window.setTimeout(() => window.print(), 300);
            });
        </script>
    @endif
</body>
</html>
