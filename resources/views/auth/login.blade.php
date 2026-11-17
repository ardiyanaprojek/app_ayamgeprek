<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">
    <title>Masuk - Ayam Geprek Istiqomah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <div class="auth-shell">
        <div class="auth-card">
            <div class="auth-brand" aria-label="Logo Ayam Geprek Istiqomah">AGI</div>
            <p class="auth-kicker">Sistem POS &amp; Inventaris</p>
            <h1 class="auth-title">Login</h1>

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username') }}"
                        placeholder="masukan username"
                        autocomplete="username"
                        required
                        autofocus
                    >
                    @error('username')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-field">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="password-toggle" aria-label="Lihat password" data-password-toggle>
                            <span aria-hidden="true">👁</span>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-remember-row mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-secondary" for="remember">Ingat saya</label>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-danger btn-login">Login</button>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger mb-0" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButtons = document.querySelectorAll('[data-password-toggle]');

            toggleButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = button.previousElementSibling;
                    const isPassword = input.type === 'password';

                    input.type = isPassword ? 'text' : 'password';
                    button.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Lihat password');
                    button.innerHTML = isPassword ? '<span aria-hidden="true">🙈</span>' : '<span aria-hidden="true">👁</span>';
                });
            });
        });
    </script>
</body>
</html>
