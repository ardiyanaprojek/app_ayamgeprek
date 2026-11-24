@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-secondary small fw-semibold mb-1">KASIR</p>
            <h1 class="h3 fw-bold mb-1">Riwayat Transaksi</h1>
            <p class="text-secondary mb-0">Cari nota dan tinjau detail pembayaran transaksi yang sudah selesai.</p>
        </div>
        <a class="btn btn-danger text-nowrap" href="{{ route('transactions.index') }}">Transaksi baru</a>
    </div>

    <section class="card">
        <div class="card-header p-3 p-md-4">
            <form class="row g-2 align-items-end" method="GET" action="{{ route('transaction-history.index') }}">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold" for="history-search">Nomor nota</label>
                    <input class="form-control" id="history-search" name="q" type="search" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nomor nota...">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="history-from">Dari tanggal</label>
                    <input class="form-control" id="history-from" name="from" type="date" value="{{ $filters['from'] ?? '' }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="history-to">Sampai tanggal</label>
                    <input class="form-control" id="history-to" name="to" type="date" value="{{ $filters['to'] ?? '' }}">
                </div>
                <div class="col-12 col-md-2">
                    <button class="btn btn-danger w-100" type="submit">Terapkan</button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover align-middle mb-0 pos-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Tanggal &amp; waktu</th>
                            <th>No. nota</th>
                            <th>Rincian menu</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Diterima</th>
                            <th class="text-end pe-4">Kembalian</th>
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
                                <td class="text-end font-numeric">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</td>
                                <td class="text-end font-numeric">Rp {{ number_format($transaction->nominal_diterima, 0, ',', '.') }}</td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <span class="font-numeric">Rp {{ number_format($transaction->kembalian, 0, ',', '.') }}</span>
                                        <a class="btn btn-sm btn-outline-danger" href="{{ route('transactions.receipt', $transaction) }}" target="_blank" title="Cetak ulang struk">
                                            Cetak
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="py-5 text-center text-secondary" colspan="6">Transaksi tidak ditemukan.</td></tr>
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
                            <div class="small mb-3">
                                @foreach ($transaction->items as $item)
                                    <span class="d-block">{{ $item->qty }}× {{ $item->product?->nama_menu ?? 'Menu dihapus' }}</span>
                                @endforeach
                            </div>
                            <div class="small text-secondary">
                                <span>Diterima <strong class="float-end text-dark font-numeric">Rp {{ number_format($transaction->nominal_diterima, 0, ',', '.') }}</strong></span>
                                <span class="d-block mt-1">Kembalian <strong class="float-end text-dark font-numeric">Rp {{ number_format($transaction->kembalian, 0, ',', '.') }}</strong></span>
                            </div>
                            <a class="btn btn-sm btn-outline-danger w-100 mt-3" href="{{ route('transactions.receipt', $transaction) }}" target="_blank" rel="noopener">
                                Cetak ulang struk
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="py-4 text-center text-secondary">Transaksi tidak ditemukan.</div>
                @endforelse
            </div>
        </div>

        @if ($transactions->hasPages())
            <div class="card-footer pos-pagination">{{ $transactions->links() }}</div>
        @endif
    </section>
@endsection
