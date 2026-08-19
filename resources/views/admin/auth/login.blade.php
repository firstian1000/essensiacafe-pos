<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - Esensia Koffie</title>
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
                <img src="{{ asset('images/logo.png') }}" alt="Esensia Koffie">
                <span>Esensia Koffie</span>
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

                <form action="{{ route('login') }}" method="POST" class="login-form" autocomplete="off">
                    @csrf

                    <label class="field-group">
                        <span>Email</span>
                        <div class="input-shell">
                            <i class="bi bi-envelope"></i>
                            <input type="email" name="email" value="" placeholder="Masukan email" autocomplete="off" required autofocus>
                        </div>
                    </label>

                    <label class="field-group">
                        <span>Password</span>
                        <div class="input-shell">
                            <i class="bi bi-key"></i>
                            <input id="password" type="password" name="password" placeholder="password" autocomplete="new-password" required>
                            <button type="button" class="password-toggle" aria-label="Tampilkan password" aria-pressed="false">
                                <i class="bi bi-eye"></i>
                            </button>
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

    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.querySelector('.password-toggle');

        passwordToggle?.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            passwordToggle.setAttribute('aria-pressed', String(isPassword));
            passwordToggle.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
            passwordToggle.querySelector('i').className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    </script>
</body>
</html>
