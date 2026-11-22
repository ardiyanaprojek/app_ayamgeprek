<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('title', 'Dashboard') - {{ $shopSetting->nama_warung }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @php
        $currentUser = auth()->user();
        $userInitial = $currentUser ? strtoupper(substr($currentUser->name, 0, 1)) : 'A';
    @endphp

    <div class="app-shell">
        <button class="sidebar-backdrop" type="button" aria-label="Tutup menu navigasi" data-sidebar-close></button>

        <aside class="app-sidebar p-3" id="app-sidebar" aria-label="Navigasi utama">
            <a class="brand d-flex align-items-center gap-2 text-decoration-none text-white mb-4" href="{{ route('dashboard') }}">
                <span class="brand-mark rounded-3 bg-danger text-white fw-bold d-inline-flex align-items-center justify-content-center">AGI</span>
                <span class="d-grid">
                    <strong class="small fw-bold text-dark">{{ $shopSetting->nama_warung }}</strong>
                    <small class="text-secondary">POS &amp; Inventaris</small>
                </span>
            </a>

            <nav class="nav nav-pills flex-column gap-1">
                <p class="sidebar-section mt-0">Operasional</p>
                <a class="nav-link sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                    <span aria-hidden="true">⌂</span><span>Dashboard</span>
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}" @if (request()->routeIs('transactions.*')) aria-current="page" @endif>
                    <span aria-hidden="true">＋</span><span>Kasir POS</span>
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('transaction-history.*') ? 'active' : '' }}" href="{{ route('transaction-history.index') }}" @if (request()->routeIs('transaction-history.*')) aria-current="page" @endif>
                    <span aria-hidden="true">◷</span><span>Riwayat Transaksi</span>
                </a>

                <p class="sidebar-section">Katalog &amp; inventaris</p>
                <a class="nav-link sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}" @if (request()->routeIs('products.*')) aria-current="page" @endif>
                    <span aria-hidden="true">▤</span><span>Katalog Menu</span>
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('ingredients.*') ? 'active' : '' }}" href="{{ route('ingredients.index') }}" @if (request()->routeIs('ingredients.*')) aria-current="page" @endif>
                    <span aria-hidden="true">▧</span><span>Bahan Baku</span>
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('stocks.index') ? 'active' : '' }}" href="{{ route('stocks.index') }}" @if (request()->routeIs('stocks.index')) aria-current="page" @endif>
                    <span aria-hidden="true">⇄</span><span>Mutasi Stok</span>
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('stocks.history') ? 'active' : '' }}" href="{{ route('stocks.history') }}" @if (request()->routeIs('stocks.history')) aria-current="page" @endif>
                    <span aria-hidden="true">◷</span><span>Riwayat Stok</span>
                </a>

                <p class="sidebar-section">Laporan</p>
                <a class="nav-link sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.transactions') }}" @if (request()->routeIs('reports.*')) aria-current="page" @endif>
                    <span aria-hidden="true">▥</span><span>Laporan Transaksi</span>
                </a>
                <a class="nav-link sidebar-link {{ request()->routeIs('closings.*') ? 'active' : '' }}" href="{{ route('closings.index') }}" @if (request()->routeIs('closings.*')) aria-current="page" @endif>
                    <span aria-hidden="true">◷</span><span>Tutup Operasional</span>
                </a>
                <p class="sidebar-section">Sistem</p>
                <a class="nav-link sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}" @if (request()->routeIs('settings.*')) aria-current="page" @endif>
                    <span aria-hidden="true">⚙</span><span>Pengaturan</span>
                </a>
            </nav>

            <div class="sidebar-footer mt-auto pt-3">
                @auth
                    <div class="d-flex align-items-center gap-2 px-1">
                        <span class="avatar rounded-circle fw-bold d-inline-flex align-items-center justify-content-center" aria-label="Profil {{ $currentUser->name }}">{{ $userInitial }}</span>
                        <span class="d-grid">
                            <strong class="small">{{ $currentUser->name }}</strong>
                            <small class="text-secondary">{{ $currentUser->username }}</small>
                        </span>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-danger btn-sm w-100">Masuk</a>
                @endauth
            </div>
        </aside>

        <div class="app-main">
            <header class="navbar app-header sticky-top px-3 px-md-4">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary btn-sm menu-toggle d-lg-none" type="button" aria-label="Buka menu navigasi" aria-controls="app-sidebar" aria-expanded="false" data-sidebar-toggle>
                        <span aria-hidden="true">☰</span>
                    </button>
                    <span class="mobile-brand-mark rounded-3 bg-danger text-white fw-bold d-lg-none">AGI</span>
                    <div class="d-grid">
                        <strong class="desktop-store-name">{{ $shopSetting->nama_warung }}</strong>
                        <strong class="mobile-store-name">{{ strtoupper($shopSetting->nama_warung) }}</strong>
                        <small class="mobile-store-location text-secondary">{{ $shopSetting->alamat ?: '-' }}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <time class="text-secondary small d-none d-xl-inline" datetime="{{ now()->toIso8601String() }}">{{ now()->format('d M Y, H:i') }}</time>
                    @auth
                        <div class="user-toolbar d-flex align-items-center gap-2">
                            <span class="text-secondary small d-none d-md-inline">{{ $currentUser->username ?? $currentUser->name }}</span>
                            <span class="avatar rounded-circle fw-bold d-inline-flex align-items-center justify-content-center" aria-label="Profil {{ $currentUser->name }}">{{ $userInitial }}</span>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary btn-sm">Keluar</button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">Masuk</a>
                    @endauth
                </div>
            </header>

            <main class="container-fluid py-4 px-3 px-md-4">
                @yield('content')
            </main>
        </div>

        <nav class="mobile-bottom-nav d-lg-none" aria-label="Navigasi bawah">
            <a class="bottom-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                <span aria-hidden="true">⌂</span><small>Beranda</small>
            </a>
            <a class="bottom-nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}" @if (request()->routeIs('transactions.*')) aria-current="page" @endif><span aria-hidden="true">＋</span><small>Kasir</small></a>
            <a class="bottom-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}" @if (request()->routeIs('products.*')) aria-current="page" @endif><span aria-hidden="true">▤</span><small>Menu</small></a>
            <a class="bottom-nav-link {{ request()->routeIs('ingredients.*') ? 'active' : '' }}" href="{{ route('ingredients.index') }}" @if (request()->routeIs('ingredients.*')) aria-current="page" @endif><span aria-hidden="true">▧</span><small>Bahan</small></a>
            <a class="bottom-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.transactions') }}" @if (request()->routeIs('reports.*')) aria-current="page" @endif><span aria-hidden="true">▥</span><small>Laporan</small></a>
        </nav>
    </div>
    @stack('scripts')
</body>
</html>
