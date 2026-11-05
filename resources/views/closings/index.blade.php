@extends('layouts.app')

@section('title', 'Tutup Operasional')

@section('content')
    <div class="mb-4">
        <p class="text-secondary small fw-semibold mb-1">OPERASIONAL HARIAN</p>
        <h1 class="h3 fw-bold mb-1">Tutup Operasional</h1>
        <p class="text-secondary mb-0">Catat uang fisik di laci dan simpan ringkasan penjualan serta stok hari ini.</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Data tutup operasional belum valid.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-4">
            <article class="card h-100"><div class="card-body">
                <p class="small text-secondary fw-bold text-uppercase mb-2">Penjualan hari ini</p>
                <strong class="h4 font-numeric">Rp {{ number_format($summary->total_sales, 0, ',', '.') }}</strong>
            </div></article>
        </div>
        <div class="col-6 col-xl-4">
            <article class="card h-100"><div class="card-body">
                <p class="small text-secondary fw-bold text-uppercase mb-2">Transaksi dilayani</p>
                <strong class="h4 font-numeric">{{ number_format($summary->transaction_count, 0, ',', '.') }} transaksi</strong>
            </div></article>
        </div>
        <div class="col-12 col-xl-4">
            <article class="card h-100"><div class="card-body">
                <p class="small text-secondary fw-bold text-uppercase mb-2">Tanggal tutup</p>
                <strong class="h4">{{ \Illuminate\Support\Carbon::parse($closingDate)->translatedFormat('l, d F Y') }}</strong>
            </div></article>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-5">
            <section class="card h-100">
                <div class="card-header py-3">
                    <h2 class="h6 fw-bold mb-1">Rekap kas fisik</h2>
                    <p class="small text-secondary mb-0">Bandingkan uang di laci dengan total transaksi tunai.</p>
                </div>
                <div class="card-body">
                    @if ($todayClosing)
                        <div class="alert alert-success mb-3">
                            <strong>Operasional hari ini sudah ditutup.</strong>
                            <div class="small mt-1">Tersimpan {{ $todayClosing->closed_at->format('H:i') }}. Tutup operasional hanya mencatat rekap; kasir tetap dapat digunakan.</div>
                        </div>
                        <dl class="row small mb-0">
                            <dt class="col-7 text-secondary">Penjualan</dt>
                            <dd class="col-5 text-end font-numeric">Rp {{ number_format($todayClosing->total_sales, 0, ',', '.') }}</dd>
                            <dt class="col-7 text-secondary">Uang fisik dihitung</dt>
                            <dd class="col-5 text-end font-numeric">Rp {{ number_format($todayClosing->cash_counted, 0, ',', '.') }}</dd>
                            <dt class="col-7 text-secondary">Selisih kas</dt>
                            <dd class="col-5 text-end fw-bold font-numeric {{ $todayClosing->cash_difference < 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($todayClosing->cash_difference, 0, ',', '.') }}</dd>
                            @if ($todayClosing->notes)
                                <dt class="col-12 text-secondary">Catatan</dt>
                                <dd class="col-12">{{ $todayClosing->notes }}</dd>
                            @endif
                        </dl>
                    @else
                        <form method="POST" action="{{ route('closings.store') }}" onsubmit="return confirm('Simpan rekap tutup operasional hari ini?')">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="cash_counted">Uang fisik di laci (Rp)</label>
                                <input class="form-control font-numeric" id="cash_counted" name="cash_counted" type="number" min="0" step="100" value="{{ old('cash_counted') }}" placeholder="Contoh: 850000" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="closing-notes">Catatan (opsional)</label>
                                <textarea class="form-control" id="closing-notes" name="notes" rows="3" maxlength="1000" placeholder="Catatan selisih kas atau operasional">{{ old('notes') }}</textarea>
                            </div>
                            <button class="btn btn-danger w-100" type="submit">Simpan tutup operasional</button>
                        </form>
                    @endif
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-7">
            <section class="card h-100">
                <div class="card-header py-3">
                    <h2 class="h6 fw-bold mb-1">Sisa stok saat ini</h2>
                    <p class="small text-secondary mb-0">Stok yang tersimpan saat penutupan akan masuk ke snapshot rekap.</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 pos-table">
                            <thead><tr><th class="ps-4">Bahan baku</th><th>Satuan</th><th class="text-end pe-4">Stok fisik</th></tr></thead>
                            <tbody>
                                @forelse ($ingredients as $ingredient)
                                    <tr>
                                        <td class="ps-4 fw-semibold">{{ $ingredient->nama_bahan }}</td>
                                        <td>{{ ucfirst($ingredient->satuan) }}</td>
                                        <td class="text-end pe-4 font-numeric">{{ $ingredient->stok_saat_ini }}</td>
                                    </tr>
                                @empty
                                    <tr><td class="py-4 text-center text-secondary" colspan="3">Belum ada data bahan baku.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <section class="card mt-4">
        <div class="card-header py-3">
            <h2 class="h6 fw-bold mb-0">Riwayat tutup operasional</h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 pos-table">
                    <thead><tr><th class="ps-4">Tanggal</th><th>Transaksi</th><th>Total penjualan</th><th>Uang fisik</th><th class="text-end pe-4">Selisih</th></tr></thead>
                    <tbody>
                        @forelse ($closings as $closing)
                            <tr>
                                <td class="ps-4">{{ $closing->closing_date->format('d M Y') }}</td>
                                <td class="font-numeric">{{ $closing->transaction_count }}</td>
                                <td class="font-numeric">Rp {{ number_format($closing->total_sales, 0, ',', '.') }}</td>
                                <td class="font-numeric">Rp {{ number_format($closing->cash_counted, 0, ',', '.') }}</td>
                                <td class="text-end pe-4 font-numeric {{ $closing->cash_difference < 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($closing->cash_difference, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-4 text-center text-secondary" colspan="5">Belum ada tutup operasional tersimpan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($todayClosing)
            <div class="card-footer small text-secondary">
                Snapshot stok saat tutup:
                @forelse ($todayClosing->stock_snapshot as $stock)
                    <span class="me-2">{{ $stock['name'] }} {{ $stock['quantity'] }} {{ $stock['unit'] }}{{ $loop->last ? '' : ',' }}</span>
                @empty
                    tidak ada bahan baku
                @endforelse
            </div>
        @endif
    </section>
@endsection
