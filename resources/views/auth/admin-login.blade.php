<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - Essensia Koffie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}?v=1">
</head>
<body>
    <main class="login-page">
        <section class="login-visual" aria-label="Essensia Koffie">
            <div class="brand-mark">
                <img src="{{ asset('images/logo.png') }}" alt="Essensia Koffie">
                <div>
                    <strong>Essensia Koffie</strong>
                    <span>Coffee & Space</span>
                </div>
            </div>

            <div class="visual-copy">
                <span class="eyebrow">Cafe Management</span>
                <h1>Kelola pesanan dengan lebih tenang.</h1>
                <p>Aman,Cepat,Informatif</p>
            </div>

            <div class="visual-stats">
                <div><i class="bi bi-receipt"></i><span>Pesanan</span></div>
                <div><i class="bi bi-cup-hot"></i><span>Menu</span></div>
                <div><i class="bi bi-credit-card"></i><span>Pembayaran</span></div>
            </div>
        </section>

        <section class="login-panel">
            <div class="mobile-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Essensia Koffie">
                <span>Essensia Koffie</span>
            </div>

            <div class="login-card">
                <div class="login-heading">
                    <span class="login-icon"><i class="bi bi-shield-lock"></i></span>
                    <div>
                        <h2>Masuk Admin</h2>
                        <p>Gunakan akun admin untuk membuka dashboard.</p>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert-error">
                        <i class="bi bi-exclamation-circle"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="login-form">
                    @csrf

                    <label class="field-group">
                        <span>Email</span>
                        <div class="input-shell">
                            <i class="bi bi-envelope"></i>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@essensia.test" autocomplete="email" required autofocus>
                        </div>
                    </label>

                    <label class="field-group">
                        <span>Password</span>
                        <div class="input-shell">
                            <i class="bi bi-key"></i>
                            <input type="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
                        </div>
                    </label>

                    <label class="remember-row">
                        <input type="checkbox" name="remember" value="1">
                        <span>Ingat perangkat ini</span>
                    </label>

                    <button type="submit" class="btn-login">
                        Masuk
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
