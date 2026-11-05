@extends('layouts.app')

@section('title', 'Master Data Bahan Baku')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-secondary small fw-semibold mb-1">DATA MASTER</p>
            <h1 class="h3 fw-bold mb-1">Master Data Bahan Baku</h1>
            <p class="text-secondary mb-0">Kelola bahan baku dan batas stok minimum warung.</p>
        </div>
        <button class="btn btn-danger text-nowrap" type="button" data-bs-toggle="modal" data-bs-target="#ingredientModal" data-mode="create">
            <span aria-hidden="true">＋</span> Tambah Bahan Baru
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
            <form class="row g-2 align-items-center" method="GET" action="{{ route('ingredients.index') }}">
                <div class="col-12 col-md-6 col-lg-5">
                    <label class="visually-hidden" for="ingredient-search">Cari nama bahan</label>
                    <input class="form-control" id="ingredient-search" name="q" type="search" value="{{ $search }}" placeholder="Cari nama bahan...">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                </div>
                @if ($search !== '')
                    <div class="col-auto">
                        <a class="btn btn-link text-secondary text-decoration-none" href="{{ route('ingredients.index') }}">Hapus pencarian</a>
                    </div>
                @endif
            </form>
        </div>

        <div class="card-body p-0">
            <div class="d-none d-lg-block table-responsive">
                <table class="table table-hover align-middle mb-0 pos-table">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" scope="col">No</th>
                            <th scope="col">Nama Bahan</th>
                            <th scope="col">Satuan</th>
                            <th scope="col">Stok Fisik Saat Ini</th>
                            <th scope="col">Batas Minimum</th>
                            <th scope="col">Status</th>
                            <th class="text-end pe-4" scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ingredients as $ingredient)
                            @php
                                $status = $ingredient->stok_saat_ini > $ingredient->stok_minimum ? 'Aman' : ($ingredient->stok_saat_ini === 0 ? 'Habis' : 'Menipis');
                            @endphp
                            <tr>
                                <td class="ps-4">{{ $ingredients->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $ingredient->nama_bahan }}</td>
                                <td>{{ ucfirst($ingredient->satuan) }}</td>
                                <td>{{ $ingredient->stok_saat_ini }} {{ $ingredient->satuan }}</td>
                                <td>{{ $ingredient->stok_minimum }} {{ $ingredient->satuan }}</td>
                                <td>
                                    @if ($status === 'Aman')
                                        <span class="pos-badge pos-badge-safe">Aman</span>
                                    @elseif ($status === 'Habis')
                                        <span class="pos-badge pos-badge-danger">Habis</span>
                                    @else
                                        <span class="pos-badge pos-badge-warning">Menipis</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-pos-edit" type="button" data-bs-toggle="modal" data-bs-target="#ingredientModal" data-mode="edit" data-id="{{ $ingredient->id }}" data-name="{{ $ingredient->nama_bahan }}" data-unit="{{ $ingredient->satuan }}" data-minimum="{{ $ingredient->stok_minimum }}">
                                        Edit
                                    </button>
                                    <form class="d-inline" method="POST" action="{{ route('ingredients.destroy', $ingredient) }}" onsubmit="return confirm('Yakin ingin menghapus bahan baku ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-5 text-center text-secondary" colspan="7">
                                    {{ $search !== '' ? 'Bahan baku tidak ditemukan.' : 'Belum ada data bahan baku.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-block d-lg-none p-3">
                @forelse ($ingredients as $ingredient)
                    @php
                        $status = $ingredient->stok_saat_ini > $ingredient->stok_minimum
                            ? 'Aman'
                            : ($ingredient->stok_saat_ini === 0 ? 'Habis' : 'Menipis');
                    @endphp
                    <article class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                                <div>
                                    <span class="small text-secondary">#{{ $ingredients->firstItem() + $loop->index }}</span>
                                    <h2 class="h6 fw-bold mb-0 mt-1">{{ $ingredient->nama_bahan }}</h2>
                                </div>
                                @if ($status === 'Aman')
                                    <span class="pos-badge pos-badge-safe">Aman</span>
                                @elseif ($status === 'Habis')
                                    <span class="pos-badge pos-badge-danger">Habis</span>
                                @else
                                    <span class="pos-badge pos-badge-warning">Menipis</span>
                                @endif
                            </div>
                            <div class="row g-2 small mb-3">
                                <div class="col-6 text-secondary">Satuan</div>
                                <div class="col-6 text-end fw-semibold">{{ ucfirst($ingredient->satuan) }}</div>
                                <div class="col-6 text-secondary">Stok fisik</div>
                                <div class="col-6 text-end fw-semibold">{{ $ingredient->stok_saat_ini }} {{ $ingredient->satuan }}</div>
                                <div class="col-6 text-secondary">Batas minimum</div>
                                <div class="col-6 text-end">{{ $ingredient->stok_minimum }} {{ $ingredient->satuan }}</div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-pos-edit flex-fill" type="button" data-bs-toggle="modal" data-bs-target="#ingredientModal" data-mode="edit" data-id="{{ $ingredient->id }}" data-name="{{ $ingredient->nama_bahan }}" data-unit="{{ $ingredient->satuan }}" data-minimum="{{ $ingredient->stok_minimum }}"
                                >Edit</button>
                                <form class="flex-fill" method="POST" action="{{ route('ingredients.destroy', $ingredient) }}" onsubmit="return confirm('Yakin ingin menghapus bahan baku ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger w-100" type="submit">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="py-4 text-center text-secondary">
                        {{ $search !== '' ? 'Bahan baku tidak ditemukan.' : 'Belum ada data bahan baku.' }}
                    </div>
                @endforelse
            </div>
        </div>

        @if ($ingredients->hasPages())
            <div class="card-footer bg-white pos-pagination">
                {{ $ingredients->links() }}
            </div>
        @endif
    </section>

    <div class="modal fade" id="ingredientModal" tabindex="-1" aria-labelledby="ingredientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="ingredient-form" method="POST" action="{{ route('ingredients.store') }}" data-store-url="{{ route('ingredients.store') }}" data-update-url-template="{{ route('ingredients.update', ['ingredient' => '__INGREDIENT__']) }}">
                    @csrf
                    <input id="ingredient-method" type="hidden" name="_method" value="POST">
                    <div class="modal-header">
                        <div>
                            <h2 class="modal-title fs-5 fw-bold" id="ingredientModalLabel">Tambah Bahan Baru</h2>
                            <!-- <p class="small text-secondary mb-0">Stok fisik awal akan dimulai dari nol.</p> -->
                        </div>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="ingredient-name">Nama Bahan</label>
                            <input class="form-control" id="ingredient-name" name="nama_bahan" type="text" maxlength="100" required placeholder="Contoh: Ayam Potong">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="ingredient-unit">Satuan</label>
                            <select class="form-select" id="ingredient-unit" name="satuan" required>
                                <option value="" disabled selected>Pilih satuan</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit }}">{{ ucfirst($unit) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label fw-semibold" for="ingredient-minimum">Batas Minimum Stok</label>
                            <input class="form-control" id="ingredient-minimum" name="stok_minimum" type="number" min="0" step="1" value="0">
                            <div class="form-text">Opsional. Digunakan sebagai pengingat stok menipis.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" type="button" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-danger" type="submit">Simpan Bahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection