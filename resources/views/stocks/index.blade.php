@extends('layouts.app')

@section('title', 'Stok')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-secondary small fw-semibold mb-1">INVENTARIS</p>
            <h1 class="h3 fw-bold mb-1">Manajemen Stok</h1>
            <p class="text-secondary mb-0">Catat pembelian baru, penyesuaian stok, dan barang rusak/basi.</p>
        </div>
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
                        <table class="table table-hover align-middle mb-0">
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
                                        <td>{{ $ingredient->stok_saat_ini }} {{ $ingredient->satuan }}</td>
                                        <td>{{ $ingredient->stok_minimum }} {{ $ingredient->satuan }}</td>
                                        <td>
                                            @if ($status === 'Aman')
                                                <span class="badge bg-success-subtle text-success">Aman</span>
                                            @elseif ($status === 'Habis')
                                                <span class="badge bg-danger-subtle text-danger">Habis</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning-emphasis">Menipis</span>
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
                                <option value="penjualan">Penjualan</option>
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

            <section class="card shadow-sm mt-4">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 fw-bold mb-0">Riwayat terbaru</h2>
                </div>
                <div class="card-body">
                    @forelse ($stockLogs as $log)
                        <div class="border rounded p-2 mb-2">
                            <div class="d-flex justify-content-between gap-2">
                                <strong>{{ $log->ingredient?->nama_bahan ?? 'Bahan' }}</strong>
                                <span class="badge bg-light text-dark">{{ $log->tipe }}</span>
                            </div>
                            <div class="small text-secondary mt-1">
                                {{ $log->jumlah }} {{ $log->ingredient?->satuan ?? 'unit' }} • {{ $log->tanggal->format('d M Y') }}
                            </div>
                            <div class="small mt-1">{{ $log->keterangan ?? 'Tanpa keterangan' }}</div>
                        </div>
                    @empty
                        <div class="text-secondary small">Belum ada riwayat mutasi stok.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
