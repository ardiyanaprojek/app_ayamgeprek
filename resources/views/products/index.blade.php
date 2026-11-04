@extends('layouts.app')

@section('title', 'Master Data Menu')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-secondary small fw-semibold mb-1">DATA MASTER</p>
            <h1 class="h3 fw-bold mb-1">Master Data Menu</h1>
            <p class="text-secondary mb-0">Kelola menu jualan dan bahan pokok yang dipakai per porsi.</p>
        </div>
        <button class="btn btn-danger text-nowrap" type="button" data-bs-toggle="modal" data-bs-target="#productModal" data-mode="create">
            <span aria-hidden="true">＋</span> Tambah Menu Baru
        </button>
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
            <strong>Data belum dapat disimpan.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="card shadow-sm">
        <div class="card-header bg-white border-0 p-3 p-md-4">
            <form class="row g-2 align-items-center" method="GET" action="{{ route('products.index') }}">
                <div class="col-12 col-md-6 col-lg-5">
                    <label class="visually-hidden" for="product-search">Cari nama menu</label>
                    <input class="form-control" id="product-search" name="q" type="search" value="{{ $search }}" placeholder="Cari nama menu...">
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

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
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
                                <td>Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</td>
                                <td>{{ $product->ingredient?->nama_bahan ?? 'Tanpa bahan utama' }}</td>
                                <td>
                                    @if ($product->ingredient_id && $product->jumlah_potong > 0)
                                        {{ $product->jumlah_potong }} {{ $product->ingredient?->satuan ?? 'unit' }}
                                    @else
                                        <span class="text-secondary">Tidak memotong stok</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#productModal" data-mode="edit" data-id="{{ $product->id }}" data-name="{{ $product->nama_menu }}" data-price="{{ $product->harga_jual }}" data-ingredient-id="{{ $product->ingredient_id ?? '' }}" data-quantity="{{ $product->jumlah_potong }}">
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
        </div>

        @if ($products->hasPages())
            <div class="card-footer bg-white">
                {{ $products->links() }}
            </div>
        @endif
    </section>

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
                        <button class="btn btn-danger" type="submit">Simpan Menu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
