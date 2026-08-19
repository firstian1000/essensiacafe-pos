<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Admin - Esensia Koffie</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}?v=2">
</head>
<body>
    <main class="login-page">
        <section class="login-visual" aria-label="Esensia Koffie">
            <div class="brand-mark">
                <img src="{{ asset('images/logo.png') }}" alt="Esensia Koffie">
                <div>
                    <strong>Esensia Koffie</strong>
                    <span>Coffee & Space</span>
                </div>
            </div>

            <div class="visual-copy">
                <span class="eyebrow">Admin Access</span>
                <h1>Mulai kelola operasional cafe.</h1>
                <p>Buat akun admin untuk membuka dashboard Esensia.</p>
            </div>

            <div class="visual-stats">
                <div><i class="bi bi-receipt"></i><span>Pesanan</span></div>
                <div><i class="bi bi-cup-hot"></i><span>Menu</span></div>
                <div><i class="bi bi-credit-card"></i><span>Pembayaran</span></div>
            </div>
        </section>

        <section class="login-panel">
            <div class="mobile-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Esensia Koffie">
                <span>Esensia Koffie</span>
            </div>

            <div class="login-card">
                <div class="login-heading">
                    <span class="login-icon"><i class="bi bi-person-plus"></i></span>
                    <div>
                        <h2>Daftar Admin</h2>
                        <p>Buat akun baru atau gunakan akun Google.</p>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert-error">
                        <i class="bi bi-exclamation-circle"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <a href="{{ route('register.google') }}" class="btn-google">
                    <i class="bi bi-google"></i>
                    Daftar dengan Google
                </a>

                <div class="login-divider"><span>atau daftar dengan email</span></div>

                <form action="{{ route('register') }}" method="POST" class="login-form" autocomplete="off">
                    @csrf

                    <label class="field-group">
                        <span>Nama</span>
                        <div class="input-shell">
                            <i class="bi bi-person"></i>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama admin" autocomplete="name" required autofocus>
                        </div>
                    </label>

                    <label class="field-group">
                        <span>Email</span>
                        <div class="input-shell">
                            <i class="bi bi-envelope"></i>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="esensia@gmail.com" autocomplete="email" required>
                        </div>
                    </label>

                    <label class="field-group">
                        <span>Password</span>
                        <div class="input-shell">
                            <i class="bi bi-key"></i>
                            <input id="password" type="password" name="password" placeholder="minimal 8 karakter" autocomplete="new-password" required>
                        </div>
                    </label>

                    <label class="field-group">
                        <span>Konfirmasi Password</span>
                        <div class="input-shell">
                            <i class="bi bi-key-fill"></i>
                            <input type="password" name="password_confirmation" placeholder="ulangi password" autocomplete="new-password" required>
                        </div>
                    </label>

                    <button type="submit" class="btn-login">
                        Daftar
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <div class="login-divider"><span><a href="{{ route('login') }}">Sudah punya akun? Masuk</a></span></div>
            </div>
        </section>
    </main>
</body>
</html>
