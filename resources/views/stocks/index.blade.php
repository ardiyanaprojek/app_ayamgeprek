@extends('layouts.app')

@section('title', 'Stok')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-secondary small fw-semibold mb-1">INVENTARIS</p>
            <h1 class="h3 fw-bold mb-1">Manajemen Stok</h1>
            <p class="text-secondary mb-0">Catat stok masuk dan bahan rusak/basi. Penjualan dipotong otomatis dari kasir.</p>
        </div>
        <a class="btn btn-outline-secondary text-nowrap" href="{{ route('stocks.history') }}">Riwayat stok</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Data stok belum valid.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <section class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 fw-bold mb-0">Stok saat ini</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 pos-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" scope="col">Bahan</th>
                                    <th scope="col">Satuan</th>
                                    <th scope="col">Stok saat ini</th>
                                    <th scope="col">Minimum</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ingredients as $ingredient)
                                    @php
                                        $status = $ingredient->stok_saat_ini > $ingredient->stok_minimum ? 'Aman' : ($ingredient->stok_saat_ini === 0 ? 'Habis' : 'Menipis');
                                    @endphp
                                    <tr>
                                        <td class="ps-4 fw-semibold">{{ $ingredient->nama_bahan }}</td>
                                        <td>{{ ucfirst($ingredient->satuan) }}</td>
                                        <td class="font-numeric">{{ $ingredient->stok_saat_ini }} {{ $ingredient->satuan }}</td>
                                        <td class="font-numeric">{{ $ingredient->stok_minimum }} {{ $ingredient->satuan }}</td>
                                        <td>
                                            @if ($status === 'Aman')
                                                <span class="pos-badge pos-badge-safe">Aman</span>
                                            @elseif ($status === 'Habis')
                                                <span class="pos-badge pos-badge-danger">Habis</span>
                                            @else
                                                <span class="pos-badge pos-badge-warning">Menipis</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="py-5 text-center text-secondary" colspan="5">Belum ada data bahan baku.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-4">
            <section class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 fw-bold mb-0">Catat mutasi stok</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('stocks.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="ingredient_id">Bahan</label>
                            <select class="form-select" id="ingredient_id" name="ingredient_id" required>
                                <option value="" selected disabled>Pilih bahan</option>
                                @foreach ($ingredients as $ingredient)
                                    <option value="{{ $ingredient->id }}">{{ $ingredient->nama_bahan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="tipe">Jenis mutasi</label>
                            <select class="form-select" id="tipe" name="tipe" required>
                                <option value="masuk">Belanja masuk</option>
                                <option value="rusak_basi">Rusak / basi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="jumlah">Jumlah</label>
                            <input class="form-control" id="jumlah" name="jumlah" type="number" min="1" step="1" value="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="keterangan">Keterangan</label>
                            <input class="form-control" id="keterangan" name="keterangan" type="text" maxlength="255" placeholder="Contoh: Belanja pagi / basi 2 porsi">
                        </div>
                        <button class="btn btn-danger w-100" type="submit">Simpan stok</button>
                    </form>
                </div>
            </section>

        </div>
    </div>
@endsection
