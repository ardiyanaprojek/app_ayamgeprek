<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Riwayat Stok</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { margin: 0 0 4px; font-size: 18px; }
        .subtitle { margin: 0 0 14px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 7px 6px; }
        th { background: #f1f5f9; text-align: left; }
        td.number, th.number { text-align: right; }
        .footer { margin-top: 14px; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <h1>{{ $shopSetting->nama_warung }}</h1>
    <div class="subtitle">
        Laporan Riwayat Stok
        @if (!empty($filters['from']) || !empty($filters['to']))
            - Periode:
            {{ $filters['from'] ?? '-' }} s/d {{ $filters['to'] ?? '-' }}
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Bahan</th>
                <th>Jenis</th>
                <th class="number">Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $log->ingredient?->nama_bahan ?? 'Bahan dihapus' }}</td>
                    <td>
                        @if ($log->tipe === 'masuk')
                            Belanja masuk
                        @elseif ($log->tipe === 'penjualan')
                            Penjualan kasir
                        @else
                            Rusak / basi
                        @endif
                    </td>
                    <td class="number">{{ $log->tipe === 'masuk' ? '+' : '-' }}{{ $log->jumlah }} {{ $log->ingredient?->satuan ?? 'unit' }}</td>
                    <td>{{ $log->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center">Tidak ada data riwayat stok.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">Dicetak pada {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
