<!DOCTYPE html>
<html lang="id">

<head>
    {{-- start metadata dan asset halaman --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">
    <title>Login - {{ $shopSetting->nama_warung }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- end metadata dan asset halaman --}}
</head>

<body class="auth-page">
    {{-- start pembungkus dan kartu login --}}
    <div class="auth-shell">
        <div class="auth-card">
            {{-- start identitas warung --}}
            <div class="auth-brand" aria-label="Logo {{ $shopSetting->nama_warung }}">GP</div>
            <p class="auth-kicker">Sistem POS & Inventaris</p>
            <h1 class="auth-title">Masuk</h1>
            {{-- end identitas warung --}}

            {{-- start formulir autentikasi --}}
            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf

                {{-- start input username dan pesan kesalahan --}}
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input id="username" name="username" type="text"
                        class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}"
                        placeholder="masukan username" autocomplete="username" required autofocus>
                    @error('username')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                {{-- end input username dan pesan kesalahan --}}

                {{-- start input password dan tombol tampilkan password --}}
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-field">
                        <input id="password" name="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password"
                            autocomplete="current-password" required>
                        <button type="button" class="password-toggle" aria-label="Lihat password" data-password-toggle>
                            <span aria-hidden="true">👁</span>
                        </button>
                    </div>
                    @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                {{-- end input password dan tombol tampilkan password --}}

                {{-- start pilihan ingat saya --}}
                <div class="auth-remember-row mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember')
                            ? 'checked' : '' }}>
                        <label class="form-check-label text-secondary" for="remember">Ingat saya</label>
                    </div>
                </div>
                {{-- end pilihan ingat saya --}}

                {{-- start tombol submit --}}
                <div class="d-grid">
                    <button type="submit" class="btn btn-danger btn-login">Login</button>
                </div>
                {{-- end tombol submit --}}

                {{-- start pesan kesalahan autentikasi --}}
                @if ($errors->any())
                <div class="alert alert-danger mb-0" role="alert">
                    {{ $errors->first() }}
                </div>
                @endif
                {{-- end pesan kesalahan autentikasi --}}
            </form>
            {{-- end formulir autentikasi --}}

        </div>
    </div>
    {{-- end pembungkus dan kartu login --}}

</body>

</html>