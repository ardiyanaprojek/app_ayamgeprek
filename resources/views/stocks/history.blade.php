@extends('layouts.app')

@section('title', 'Riwayat Stok')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-secondary small fw-semibold mb-1">INVENTARIS</p>
            <h1 class="h3 fw-bold mb-1">Riwayat Stok</h1>
            <p class="text-secondary mb-0">Belanja masuk, bahan rusak/basi, dan stok terpakai dari transaksi kasir.</p>
        </div>
        <a class="btn btn-danger text-nowrap" href="{{ route('stocks.index') }}">Catat mutasi stok</a>
    </div>

    <section class="card">
        <div class="card-header p-3 p-md-4">
            <form class="row g-2 align-items-end" method="GET" action="{{ route('stocks.history') }}">
                <div class="col-12 col-sm-4">
                    <label class="form-label small fw-semibold" for="stock-from">Dari tanggal</label>
                    <input class="form-control" id="stock-from" name="from" type="date" value="{{ $filters['from'] ?? '' }}">
                </div>
                <div class="col-12 col-sm-4">
                    <label class="form-label small fw-semibold" for="stock-to">Sampai tanggal</label>
                    <input class="form-control" id="stock-to" name="to" type="date" value="{{ $filters['to'] ?? '' }}">
                </div>
                <div class="col-8 col-sm-3">
                    <label class="form-label small fw-semibold" for="stock-type">Jenis</label>
                    <select class="form-select" id="stock-type" name="tipe">
                        <option value="">Semua jenis</option>
                        <option value="masuk" @selected(($filters['tipe'] ?? '') === 'masuk')>Belanja masuk</option>
                        <option value="rusak_basi" @selected(($filters['tipe'] ?? '') === 'rusak_basi')>Rusak / basi</option>
                        <option value="penjualan" @selected(($filters['tipe'] ?? '') === 'penjualan')>Penjualan kasir</option>
                    </select>
                </div>
                <div class="col-4 col-sm-1">
                    <button class="btn btn-danger w-100" type="submit">Cari</button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover align-middle mb-0 pos-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Tanggal</th>
                            <th>Bahan</th>
                            <th>Jenis mutasi</th>
                            <th class="text-end">Jumlah</th>
                            <th class="pe-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="ps-4">{{ $log->tanggal->format('d M Y') }}</td>
                                <td class="fw-semibold">{{ $log->ingredient?->nama_bahan ?? 'Bahan dihapus' }}</td>
                                <td>
                                    @if ($log->tipe === 'masuk')
                                        <span class="pos-badge pos-badge-safe">Belanja masuk</span>
                                    @elseif ($log->tipe === 'penjualan')
                                        <span class="pos-badge pos-badge-warning">Penjualan kasir</span>
                                    @else
                                        <span class="pos-badge pos-badge-danger">Rusak / basi</span>
                                    @endif
                                </td>
                                <td class="text-end font-numeric">{{ $log->tipe === 'masuk' ? '+' : '−' }}{{ $log->jumlah }} {{ $log->ingredient?->satuan ?? 'unit' }}</td>
                                <td class="pe-4">{{ $log->keterangan ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-5 text-center text-secondary" colspan="5">Belum ada riwayat stok untuk filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-lg-none p-3">
                @forelse ($logs as $log)
                    <article class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between gap-2">
                                <strong>{{ $log->ingredient?->nama_bahan ?? 'Bahan dihapus' }}</strong>
                                <span class="font-numeric fw-semibold">{{ $log->tipe === 'masuk' ? '+' : '−' }}{{ $log->jumlah }} {{ $log->ingredient?->satuan ?? 'unit' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center gap-2 mt-2">
                                <small class="text-secondary">{{ $log->tanggal->format('d M Y') }}</small>
                                @if ($log->tipe === 'masuk')
                                    <span class="pos-badge pos-badge-safe">Belanja masuk</span>
                                @elseif ($log->tipe === 'penjualan')
                                    <span class="pos-badge pos-badge-warning">Penjualan kasir</span>
                                @else
                                    <span class="pos-badge pos-badge-danger">Rusak / basi</span>
                                @endif
                            </div>
                            <p class="small text-secondary mb-0 mt-2">{{ $log->keterangan ?? 'Tanpa keterangan' }}</p>
                        </div>
                    </article>
                @empty
                    <div class="py-4 text-center text-secondary">Belum ada riwayat stok untuk filter ini.</div>
                @endforelse
            </div>
        </div>

        @if ($logs->hasPages())
            <div class="card-footer pos-pagination">{{ $logs->links() }}</div>
        @endif
    </section>
@endsection
