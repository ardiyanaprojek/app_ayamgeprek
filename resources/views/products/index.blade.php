@extends('layouts.app')

@section('title', 'Master Data Menu')

@section('content')
    {{-- start judul halaman dan tombol tambah menu --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-secondary small fw-semibold mb-1">DATA MASTER</p>
            <h1 class="h3 fw-bold mb-1">Master Data Menu</h1>
            <p class="text-secondary mb-0">Kelola menu jualan dan bahan pokok yang dipakai per porsi.</p>
        </div>
        <button class="btn btn-primary text-nowrap" type="button" data-bs-toggle="modal" data-bs-target="#productModal" data-mode="create">
            <span aria-hidden="true">＋</span> Tambah Menu Baru
        </button>
    </div>
    {{-- end judul halaman dan tombol tambah menu --}}

    {{-- start notifikasi status dan validasi --}}
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
            <strong>Data belum dapat disimpan.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{-- end notifikasi status dan validasi --}}

    {{-- start daftar menu --}}
    <section class="card shadow-sm">
        {{-- start pencarian menu --}}
        <div class="card-header bg-white border-0 p-3 p-md-4">
            <form class="row g-2 align-items-center" method="GET" action="{{ route('products.index') }}">
                <div class="col-12 col-md-6 col-lg-5">
                    <label class="visually-hidden" for="product-search">Cari nama menu</label>
                    <input class="form-control" id="product-search" name="q" type="search" value="{{ $search }}" placeholder="⌕  Cari nama menu...">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                </div>
                @if ($search !== '')
                    <div class="col-auto">
                        <a class="btn btn-link text-secondary text-decoration-none" href="{{ route('products.index') }}">Hapus pencarian</a>
                    </div>
                @endif
            </form>
        </div>
        {{-- end pencarian menu --}}

        <div class="card-body p-0">
            {{-- start tabel menu untuk desktop --}}
            <div class="table-responsive desktop-product-table">
                <table class="table table-hover align-middle mb-0 pos-table">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" scope="col">No</th>
                            <th scope="col">Nama Menu</th>
                            <th scope="col">Harga Jual</th>
                            <th scope="col">Bahan Utama</th>
                            <th scope="col">Potong / Porsi</th>
                            <th class="text-end pe-4" scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td class="ps-4">{{ $products->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $product->nama_menu }}</td>
                                <td class="font-numeric">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</td>
                                <td>{{ $product->ingredient?->nama_bahan ?? 'Tanpa bahan utama' }}</td>
                                <td>
                                    @if ($product->ingredient_id && $product->jumlah_potong > 0)
                                        {{ $product->jumlah_potong }} {{ $product->ingredient?->satuan ?? 'unit' }}
                                    @else
                                        <span class="text-secondary">Tidak memotong stok</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-pos-edit" type="button" data-bs-toggle="modal" data-bs-target="#productModal" data-mode="edit" data-id="{{ $product->id }}" data-name="{{ $product->nama_menu }}" data-price="{{ $product->harga_jual }}" data-ingredient-id="{{ $product->ingredient_id ?? '' }}" data-quantity="{{ $product->jumlah_potong }}">
                                        Edit
                                    </button>
                                    <form class="d-inline" method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-5 text-center text-secondary" colspan="6">
                                    {{ $search !== '' ? 'Menu tidak ditemukan.' : 'Belum ada data menu.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- end tabel menu untuk desktop --}}

            {{-- start kartu menu untuk layar kecil --}}
            <div class="product-mobile-list d-none p-3">
                @forelse ($products as $product)
                    <article class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                                <div>
                                    <span class="small text-secondary">Menu #{{ $products->firstItem() + $loop->index }}</span>
                                    <h2 class="h6 fw-bold mb-0 mt-1">{{ $product->nama_menu }}</h2>
                                </div>
                                <strong class="font-numeric text-nowrap">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</strong>
                            </div>
                            <div class="small text-secondary mb-3">
                                {{ $product->ingredient?->nama_bahan ?? 'Tanpa bahan utama' }}
                                @if ($product->ingredient_id && $product->jumlah_potong > 0)
                                    · {{ $product->jumlah_potong }} {{ $product->ingredient?->satuan ?? 'unit' }} / porsi
                                @else
                                    · Tidak memotong stok
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-pos-edit flex-fill" type="button" data-bs-toggle="modal" data-bs-target="#productModal" data-mode="edit" data-id="{{ $product->id }}" data-name="{{ $product->nama_menu }}" data-price="{{ $product->harga_jual }}" data-ingredient-id="{{ $product->ingredient_id ?? '' }}" data-quantity="{{ $product->jumlah_potong }}">Edit menu</button>
                                <form class="flex-fill" method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger w-100" type="submit">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="py-4 text-center text-secondary">
                        {{ $search !== '' ? 'Menu tidak ditemukan.' : 'Belum ada data menu.' }}
                    </div>
                @endforelse
            </div>
            {{-- end kartu menu untuk layar kecil --}}
        </div>

        {{-- start navigasi halaman --}}
        @if ($products->hasPages())
            <div class="card-footer bg-white pos-pagination">
                {{ $products->links() }}
            </div>
        @endif
        {{-- end navigasi halaman --}}
    </section>
    {{-- end daftar menu --}}

    {{-- start modal tambah dan edit menu --}}
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="product-form" method="POST" action="{{ route('products.store') }}" data-store-url="{{ route('products.store') }}" data-update-url-template="{{ route('products.update', ['product' => '__PRODUCT__']) }}">
                    @csrf
                    <input id="product-method" type="hidden" name="_method" value="POST">
                    <div class="modal-header">
                        <div>
                            <h2 class="modal-title fs-5 fw-bold" id="productModalLabel">Tambah Menu Baru</h2>
                        </div>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="product-name">Nama Menu</label>
                            <input class="form-control" id="product-name" name="nama_menu" type="text" maxlength="120" required placeholder="Contoh: Ayam Geprek Dada">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="product-price">Harga Jual</label>
                            <input class="form-control" id="product-price" name="harga_jual" type="number" min="0" step="100" required placeholder="15000">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="product-ingredient">Bahan Utama</label>
                            <select class="form-select" id="product-ingredient" name="ingredient_id">
                                <option value="">Tanpa bahan utama</option>
                                @foreach ($ingredients as $ingredient)
                                    <option value="{{ $ingredient->id }}">{{ $ingredient->nama_bahan }} ({{ $ingredient->satuan }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label fw-semibold" for="product-quantity">Jumlah Potong per Porsi</label>
                            <input class="form-control" id="product-quantity" name="jumlah_potong" type="number" min="0" step="1" value="0">
                            <div class="form-text">Kosongkan atau isi 0 jika menu tidak memotong stok bahan utama.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" type="button" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit">Simpan Menu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- end modal tambah dan edit menu --}}
@endsection
