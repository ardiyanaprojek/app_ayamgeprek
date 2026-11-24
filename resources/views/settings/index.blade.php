@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-secondary small fw-semibold mb-1">SISTEM</p>
            <h1 class="h3 fw-bold mb-1">Pengaturan</h1>
            <p class="text-secondary mb-0">Kelola akun pengguna dan identitas warung.</p>
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
            <strong>Perubahan belum dapat disimpan.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-6">
            <section class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 fw-bold mb-0">Konfigurasi Akun</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.account.update') }}" onsubmit="return confirm('Simpan perubahan akun?')">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="account-name">Nama pengguna</label>
                            <input class="form-control" id="account-name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="account-username">Username</label>
                            <input class="form-control" id="account-username" name="username" type="text" value="{{ old('username', auth()->user()->username) }}" maxlength="50" required>
                        </div>
                        <hr>
                        <p class="small text-secondary">Isi bagian password hanya jika ingin mengganti password.</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="current-password">Password lama</label>
                            <input class="form-control" id="current-password" name="current_password" type="password" autocomplete="current-password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="account-password">Password baru</label>
                            <input class="form-control" id="account-password" name="password" type="password" minlength="6" autocomplete="new-password">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="account-password-confirmation">Konfirmasi password baru</label>
                            <input class="form-control" id="account-password-confirmation" name="password_confirmation" type="password" minlength="6" autocomplete="new-password">
                        </div>
                        <button class="btn btn-danger" type="submit">Simpan akun</button>
                    </form>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-6">
            <section class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 fw-bold mb-0">Kelola Warung</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.shop.update') }}" onsubmit="return confirm('Simpan informasi warung?')">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="shop-name">Nama warung</label>
                            <input class="form-control" id="shop-name" name="nama_warung" type="text" value="{{ old('nama_warung', $shop->nama_warung) }}" maxlength="120" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="shop-address">Alamat</label>
                            <textarea class="form-control" id="shop-address" name="alamat" rows="3" maxlength="255">{{ old('alamat', $shop->alamat) }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="shop-phone">Nomor telepon</label>
                            <input class="form-control" id="shop-phone" name="telepon" type="text" value="{{ old('telepon', $shop->telepon) }}" maxlength="30">
                        </div>
                        <button class="btn btn-danger" type="submit">Simpan informasi warung</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
