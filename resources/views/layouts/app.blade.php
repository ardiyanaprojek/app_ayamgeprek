<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('title', 'Dashboard') - Ayam Geprek Istiqomah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <button class="sidebar-backdrop" type="button" aria-label="Tutup menu navigasi" data-sidebar-close></button>

        <aside class="app-sidebar bg-dark text-white p-3" id="app-sidebar" aria-label="Navigasi utama">
            <a class="brand d-flex align-items-center gap-2 text-decoration-none text-white mb-4" href="{{ route('dashboard') }}">
                <span class="brand-mark rounded-3 bg-danger fw-bold d-inline-flex align-items-center justify-content-center">AGI</span>
                <span class="d-grid">
                    <strong class="small fw-bold">AYAM GEPREK ISTIQOMAH</strong>
                    <small class="text-secondary">Kelola Warung</small>
                </span>
            </a>

            <div class="border-top border-secondary opacity-25 mb-3"></div>
            <p class="text-uppercase text-secondary small fw-bold mb-2 px-2">Navigasi utama</p>

            <nav class="nav nav-pills flex-column gap-1">
                <a class="nav-link sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                    <span aria-hidden="true">▦</span><span>Dashboard</span>
                </a>
                <a class="nav-link sidebar-link" href="#stok">
                    <span aria-hidden="true">▤</span><span>Stok</span>
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}" @if (request()->routeIs('transactions.*')) aria-current="page" @endif>
                    <span aria-hidden="true">＋</span><span>Transaksi</span>
                </a>
                <a class="nav-link sidebar-link" href="#riwayat">
                    <span aria-hidden="true">◷</span><span>Riwayat Trx</span>
                </a>
                <a class="nav-link sidebar-link" href="#laporan">
                    <span aria-hidden="true">▥</span><span>Laporan</span>
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}" @if (request()->routeIs('products.*')) aria-current="page" @endif>
                    <span aria-hidden="true">☷</span><span>Data Master Menu</span>
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('ingredients.*') ? 'active' : '' }}" href="{{ route('ingredients.index') }}" @if (request()->routeIs('ingredients.*')) aria-current="page" @endif>
                    <span aria-hidden="true">▧</span><span>Data Master Bahan Baku</span>
                </a>
                <a class="nav-link sidebar-link" href="#pengaturan">
                    <span aria-hidden="true">⚙</span><span>Pengaturan</span>
                </a>
            </nav>

            <div class="sidebar-footer mt-auto pt-3">
                <button class="btn btn-warning w-100 text-start d-flex align-items-center gap-2 mb-3" type="button">
                    <span aria-hidden="true">◐</span>
                    <span class="d-grid">
                        <strong class="small text-uppercase">Tutup Warung</strong>
                        <small>Hitung laci kas</small>
                    </span>
                </button>
                <div class="d-flex align-items-center gap-2 px-1">
                    <span class="avatar rounded-circle bg-secondary fw-bold d-inline-flex align-items-center justify-content-center">P</span>
                    <span class="d-grid">
                        <strong class="small">Pemilik</strong>
                        <a class="small text-danger text-decoration-none" href="#keluar">Keluar akun</a>
                    </span>
                </div>
            </div>
        </aside>

        <div class="app-main">
            <header class="navbar bg-white border-bottom sticky-top px-3 px-md-4">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary btn-sm menu-toggle d-lg-none" type="button" aria-label="Buka menu navigasi" aria-controls="app-sidebar" aria-expanded="false" data-sidebar-toggle>
                        <span aria-hidden="true">☰</span>
                    </button>
                    <span class="mobile-brand-mark rounded-3 bg-danger text-white fw-bold d-lg-none">AGI</span>
                    <div class="d-grid">
                        <strong class="desktop-store-name">Ayam Geprek Istiqomah</strong>
                        <strong class="mobile-store-name">AYAM GEPREK ISTIQOMAH</strong>
                        <small class="mobile-store-location text-secondary">PADAKASIH</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <time class="text-secondary small d-none d-sm-inline" datetime="{{ now()->toIso8601String() }}">{{ now()->format('d M Y, H:i') }}</time>
                    <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2"><span class="status-dot"></span>Warung buka</span>
                </div>
            </header>

            <main class="container-fluid py-4 px-3 px-md-4">
                @yield('content')
            </main>
        </div>

        <nav class="mobile-bottom-nav d-lg-none" aria-label="Navigasi bawah">
            <a class="bottom-nav-link active" href="{{ route('dashboard') }}" aria-current="page">
                <span aria-hidden="true">▦</span><small>Dashboard</small>
            </a>
            <a class="bottom-nav-link" href="#stok"><span aria-hidden="true">▤</span><small>Stok</small></a>
            <a class="bottom-nav-link" href="{{ route('transactions.index') }}"><span aria-hidden="true">＋</span><small>Transaksi</small></a>
            <a class="bottom-nav-link" href="#riwayat"><span aria-hidden="true">◷</span><small>Riwayat</small></a>
            <a class="bottom-nav-link" href="#laporan"><span aria-hidden="true">▥</span><small>Laporan</small></a>
        </nav>
    </div>
    @stack('scripts')
</body>
</html>
