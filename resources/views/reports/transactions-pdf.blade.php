<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { margin: 0 0 4px; font-size: 18px; }
        .subtitle { margin: 0 0 14px; color: #64748b; }
        .summary { margin-bottom: 12px; }
        .summary strong { font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 7px 6px; }
        th { background: #f1f5f9; text-align: left; }
        td.number, th.number { text-align: right; }
        .footer { margin-top: 14px; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <h1>{{ $shopSetting->nama_warung }}</h1>
    <div class="subtitle">Laporan Transaksi - {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}</div>
    <div class="summary">
        Total penjualan: <strong>Rp {{ number_format($summary->total_sales, 0, ',', '.') }}</strong>
        &nbsp;&nbsp; Jumlah transaksi: <strong>{{ number_format($summary->transaction_count, 0, ',', '.') }}</strong>
    </div>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>No. Nota</th>
                <th>Item</th>
                <th>Metode</th>
                <th class="number">Total</th>
                <th class="number">Kembalian</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->nomor_struk }}</td>
                    <td>
                        @foreach ($transaction->items as $item)
                            {{ $item->qty }}x {{ $item->product?->nama_menu ?? 'Menu dihapus' }}@if (!$loop->last)<br>@endif
                        @endforeach
                    </td>
                    <td>{{ $transaction->metode_bayar }}</td>
                    <td class="number">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($transaction->kembalian, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center">Tidak ada transaksi pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">Dicetak pada {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
