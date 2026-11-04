@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-secondary small fw-semibold mb-1">OPERASIONAL · {{ now()->format('d M Y') }}</p>
            <h1 class="h3 fw-bold mb-1">Ringkasan hari ini</h1>
            <p class="text-secondary mb-0">Menampikan ringkasan informasi penjualan hari ini.</p>
        </div>
        <a class="btn btn-danger text-nowrap" href="{{ route('transactions.index') }}"><span aria-hidden="true">＋</span> Transaksi baru</a>
    </div>

    <section class="row g-3 mb-4" aria-label="Ringkasan penjualan dan stok">
        <div class="col-6 col-xl-3">
            <article class="card h-100 shadow-sm border-start border-4 border-success">
                <div class="card-body">
                    <p class="small text-secondary fw-bold text-uppercase mb-2">↗ Total penjualan tunai</p>
                    <strong class="h4 fw-bold d-block mb-2">Rp 850.000</strong>
                    <span class="small text-success fw-semibold">100% uang di laci</span>
                </div>
            </article>
        </div>
        <div class="col-6 col-xl-3">
            <article class="card h-100 shadow-sm border-start border-4 border-info">
                <div class="card-body">
                    <p class="small text-secondary fw-bold text-uppercase mb-2">▤ Nota transaksi</p>
                    <strong class="h4 fw-bold d-block mb-2">52 Pesanan</strong>
                    <span class="small text-secondary">Rata-rata: Rp 16.350</span>
                </div>
            </article>
        </div>
        <div class="col-6 col-xl-3">
            <article class="card h-100 shadow-sm border-start border-4 border-danger">
                <div class="card-body">
                    <p class="small text-secondary fw-bold text-uppercase mb-2">♨ Sisa ayam potong</p>
                    <strong class="h4 fw-bold d-block mb-2">12 Potong</strong>
                    <span class="small text-danger fw-semibold">● Menipis · Batas: 10</span>
                </div>
            </article>
        </div>
        <div class="col-6 col-xl-3">
            <article class="card h-100 shadow-sm border-start border-4 border-warning">
                <div class="card-body">
                    <p class="small text-secondary fw-bold text-uppercase mb-2">◈ Sisa usus &amp; tahu</p>
                    <strong class="h4 fw-bold d-block mb-2">8 Porsi / 14 Biji</strong>
                    <span class="small text-success fw-semibold">● Stok aman</span>
                </div>
            </article>
        </div>
    </section>

    <div class="row g-3">
        <section class="col-12 col-xl-7" id="stok">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white d-flex align-items-start justify-content-between gap-3 py-3">
                    <div>
                        <h2 class="h6 fw-bold mb-1">Sisa stok fisik bahan utama</h2>
                        <p class="small text-secondary mb-0">Sisa malam ini otomatis menjadi stok buka esok pagi.</p>
                    </div>
                    <a class="small text-danger fw-semibold text-nowrap" href="{{ route('stocks.index') }}">Lihat stok →</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Bahan baku</th>
                                    <th scope="col">Stok awal</th>
                                    <th scope="col">Terjual</th>
                                    <th scope="col">Sisa fisik</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">Ayam Potong</th>
                                    <td>50 ptg</td>
                                    <td class="text-danger">−38 ptg</td>
                                    <td class="fw-semibold">12 potong</td>
                                    <td><span class="badge bg-danger-subtle text-danger">Menipis</span></td>
                                </tr>
                                <tr>
                                    <th scope="row">Usus Bersih</th>
                                    <td>20 prs</td>
                                    <td class="text-danger">−12 prs</td>
                                    <td class="fw-semibold">8 porsi</td>
                                    <td><span class="badge bg-success-subtle text-success">Aman</span></td>
                                </tr>
                                <tr>
                                    <th scope="row">Tahu Mentah</th>
                                    <td>25 biji</td>
                                    <td class="text-danger">−11 biji</td>
                                    <td class="fw-semibold">14 biji</td>
                                    <td><span class="badge bg-success-subtle text-success">Aman</span></td>
                                </tr>
                                <tr>
                                    <th scope="row">Telur Ayam</th>
                                    <td>30 btr</td>
                                    <td class="text-danger">−18 btr</td>
                                    <td class="fw-semibold">12 butir</td>
                                    <td><span class="badge bg-success-subtle text-success">Aman</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- <div class="alert alert-primary small mt-3 mb-0" role="note">
                        <strong>Pengingat SOP dapur saat tutup</strong>
                        <p class="mb-1 mt-2">Ayam ungkep mentah wajib disimpan di chiller (0°–4°C). Ayam yang sudah ditepungi wajib digoreng setengah matang.</p>
                        <p class="mb-0">Catat bahan basi atau rusak pada menu Tutup Warung.</p>
                    </div> --}}
                </div>
            </div>
        </section>

        <section class="col-12 col-xl-5" id="riwayat">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                    <div>
                        <h2 class="h6 fw-bold mb-1">Bon transaksi berjalan</h2>
                        <p class="small text-secondary mb-0">Riwayat transaksi kasir hari ini.</p>
                    </div>
                    <a class="text-secondary text-decoration-none" href="#riwayat" aria-label="Lihat semua transaksi">•••</a>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="list-group list-group-flush mb-3">
                        <article class="list-group-item px-0">
                            <div class="d-flex justify-content-between gap-2"><strong>#TRX-0052</strong><strong>Rp 30.000</strong></div>
                            <div class="d-flex justify-content-between gap-2 mt-1 small text-secondary"><span>2× Geprek Dada, 2× Es Teh</span><span class="text-success text-nowrap">19:30 · Tunai pas</span></div>
                        </article>
                        <article class="list-group-item px-0">
                            <div class="d-flex justify-content-between gap-2"><strong>#TRX-0051</strong><strong>Rp 15.000</strong></div>
                            <div class="d-flex justify-content-between gap-2 mt-1 small text-secondary"><span>1× Geprek Paha, 1× Nasi</span><span class="text-success text-nowrap">19:22 · Tunai 20k</span></div>
                        </article>
                        <article class="list-group-item px-0">
                            <div class="d-flex justify-content-between gap-2"><strong>#TRX-0050</strong><strong>Rp 42.000</strong></div>
                            <div class="d-flex justify-content-between gap-2 mt-1 small text-secondary"><span>2× Geprek Dada, 2× Usus</span><span class="text-success text-nowrap">19:05 · Tunai 50k</span></div>
                        </article>
                    </div>
                    <a class="btn btn-danger mt-auto d-flex justify-content-between align-items-center" id="transaksi" href="#transaksi">
                        Buka kasir transaksi <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
