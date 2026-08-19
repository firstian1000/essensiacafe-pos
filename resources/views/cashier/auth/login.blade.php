<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Kasir - Esensia Koffie</title>
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
                <span class="eyebrow">Cashier Access</span>
                <h1>Transaksi kasir lebih cepat dan rapi.</h1>
                <p>Fokus pada pemesanan langsung, pembayaran, dan cetak nota.</p>
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
                    <span class="login-icon"><i class="bi bi-calculator"></i></span>
                    <div>
                        <h2>Masuk Kasir</h2>
                        <p>Gunakan akun kasir untuk membuka halaman transaksi.</p>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert-error">
                        <i class="bi bi-exclamation-circle"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('cashier.login') }}" method="POST" class="login-form" autocomplete="off">
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
                        Masuk Kasir
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>
        </section>
    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.querySelector('.password-toggle');
        const loginForm = document.querySelector('.login-form');
        let loginSubmitting = false;

        passwordToggle?.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            passwordToggle.setAttribute('aria-pressed', String(isPassword));
            passwordToggle.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
            passwordToggle.querySelector('i').className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });

        loginForm?.addEventListener('submit', async (event) => {
            if (loginSubmitting) return;

            event.preventDefault();

            try {
                const response = await fetch(@json(route('csrf.token')), {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                });

                if (response.ok) {
                    const data = await response.json();
                    const tokenInput = loginForm.querySelector('input[name="_token"]');
                    if (data.token && tokenInput) {
                        tokenInput.value = data.token;
                    }
                }
            } catch (error) {
                console.log('Gagal refresh token login kasir:', error);
            }

            loginSubmitting = true;
            loginForm.requestSubmit();
        });
    </script>
</body>
</html>
