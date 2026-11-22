@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-secondary small fw-semibold mb-1">LAPORAN</p>
            <h1 class="h3 fw-bold mb-1">Laporan Transaksi</h1>
            <p class="text-secondary mb-0">Rekap penjualan tunai dan rincian transaksi berdasarkan periode.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('transaction-history.index') }}">Riwayat transaksi</a>
            <a class="btn btn-outline-danger" href="{{ route('reports.transactions.pdf', ['from' => $from, 'to' => $to]) }}">Cetak PDF</a>
            <a class="btn btn-outline-secondary" href="{{ route('closings.index') }}">Tutup operasional <span aria-hidden="true">→</span></a>
        </div>
    </div>

    <section class="row g-3 mb-4" aria-label="Ringkasan laporan">
        <div class="col-6 col-xl-6">
            <article class="card h-100">
                <div class="card-body">
                    <p class="small text-secondary fw-bold text-uppercase mb-2">Total penjualan</p>
                    <strong class="h4 fw-bold font-numeric">Rp {{ number_format($summary->total_sales, 0, ',', '.') }}</strong>
                </div>
            </article>
        </div>
        <div class="col-6 col-xl-6">
            <article class="card h-100">
                <div class="card-body">
                    <p class="small text-secondary fw-bold text-uppercase mb-2">Jumlah transaksi</p>
                    <strong class="h4 fw-bold font-numeric">{{ number_format($summary->transaction_count, 0, ',', '.') }} transaksi</strong>
                </div>
            </article>
        </div>
    </section>

    <section class="card">
        <div class="card-header p-3 p-md-4">
            <form class="row g-2 align-items-end" method="GET" action="{{ route('reports.transactions') }}">
                <div class="col-12 col-sm-5 col-lg-4">
                    <label class="form-label small fw-semibold" for="report-from">Dari tanggal</label>
                    <input class="form-control" id="report-from" name="from" type="date" value="{{ $from }}" required>
                </div>
                <div class="col-12 col-sm-5 col-lg-4">
                    <label class="form-label small fw-semibold" for="report-to">Sampai tanggal</label>
                    <input class="form-control" id="report-to" name="to" type="date" value="{{ $to }}" required>
                </div>
                <div class="col-12 col-sm-2">
                    <button class="btn btn-danger w-100" type="submit">Terapkan</button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover align-middle mb-0 pos-table">
                    <thead>
                        <tr>
                            <th class="ps-4" scope="col">Waktu</th>
                            <th scope="col">No. Nota</th>
                            <th scope="col">Item</th>
                            <th scope="col">Pembayaran</th>
                            <th class="text-end" scope="col">Total</th>
                            <th class="text-end pe-4" scope="col">Kembalian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td class="ps-4">{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                <td class="fw-semibold">{{ $transaction->nomor_struk }}</td>
                                <td>
                                    @foreach ($transaction->items as $item)
                                        <span class="d-block">{{ $item->qty }}× {{ $item->product?->nama_menu ?? 'Menu dihapus' }}</span>
                                    @endforeach
                                </td>
                                <td><span class="pos-badge pos-badge-safe">{{ $transaction->metode_bayar }}</span></td>
                                <td class="text-end font-numeric">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</td>
                                <td class="text-end pe-4 font-numeric">Rp {{ number_format($transaction->kembalian, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-5 text-center text-secondary" colspan="6">Tidak ada transaksi pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-lg-none p-3">
                @forelse ($transactions as $transaction)
                    <article class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between gap-2">
                                <strong>{{ $transaction->nomor_struk }}</strong>
                                <strong class="font-numeric">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</strong>
                            </div>
                            <p class="small text-secondary mb-2">{{ $transaction->created_at->format('d M Y, H:i') }} · {{ $transaction->metode_bayar }}</p>
                            <div class="small mb-2">
                                @foreach ($transaction->items as $item)
                                    <span class="d-block">{{ $item->qty }}× {{ $item->product?->nama_menu ?? 'Menu dihapus' }}</span>
                                @endforeach
                            </div>
                            <div class="small text-secondary">Kembalian <span class="float-end font-numeric">Rp {{ number_format($transaction->kembalian, 0, ',', '.') }}</span></div>
                        </div>
                    </article>
                @empty
                    <div class="py-4 text-center text-secondary">Tidak ada transaksi pada periode ini.</div>
                @endforelse
            </div>
        </div>

        @if ($transactions->hasPages())
            <div class="card-footer pos-pagination">{{ $transactions->links() }}</div>
        @endif
    </section>
@endsection
