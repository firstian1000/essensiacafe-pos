<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AdminLoginController extends Controller
{
    private const GOOGLE_ADMIN_EMAILS = [
        'firstian1000@gmail.com',
    ];

    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('dashboard');
        }

        return view('admin.auth.login');
    }

    public function showRegisterForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('dashboard');
        }

        return view('admin.auth.register');
    }

    public function showCashierLoginForm()
    {
        if (Auth::guard('cashier')->check()) {
            return redirect()->route('cashier.index');
        }

        return view('cashier.auth.login');
    }

    public function redirectToGoogle(Request $request)
    {
        return $this->startGoogleAuth($request, 'login');
    }

    public function redirectToGoogleRegister(Request $request)
    {
        return $this->startGoogleAuth($request, 'register', 'admin');
    }

    private function startGoogleAuth(Request $request, string $mode, ?string $guard = null)
    {
        $redirectUri = $this->googleRedirectUri();

        if (! config('services.google.client_id') || ! config('services.google.client_secret') || ! $redirectUri) {
            return redirect()->route($this->loginRouteForGuard($guard ?: $request->query('guard', 'admin')))->withErrors([
                'email' => 'Login Google belum aktif. Isi GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, dan GOOGLE_REDIRECT_URI.',
            ]);
        }

        $guard = $guard ?: ($request->query('guard') === 'cashier' ? 'cashier' : 'admin');
        $state = Str::random(40);
        session([
            'google_oauth_state' => $state,
            'google_oauth_mode' => $mode,
            'google_oauth_guard' => $guard,
        ]);

        $query = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
    }

    public function handleGoogleCallback(Request $request)
    {
        $requestedGuard = session('google_oauth_guard', 'admin');
        $loginRoute = $this->loginRouteForGuard($requestedGuard);

        if (! hash_equals((string) session('google_oauth_state'), (string) $request->query('state'))) {
            return redirect()->route($loginRoute)->withErrors([
                'email' => 'Sesi login Google tidak valid. Silakan coba lagi.',
            ]);
        }

        $mode = session('google_oauth_mode', 'login');
        $request->session()->forget(['google_oauth_state', 'google_oauth_mode', 'google_oauth_guard']);
        $redirectUri = $this->googleRedirectUri();

        if ($request->filled('error')) {
            return redirect()->route($loginRoute)->withErrors([
                'email' => 'Login Google dibatalkan atau tidak diizinkan.',
            ]);
        }

        if (! $request->filled('code')) {
            return redirect()->route($loginRoute)->withErrors([
                'email' => 'Kode login Google tidak ditemukan. Silakan coba lagi.',
            ]);
        }

        try {
            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'code' => $request->query('code'),
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
            ])->throw()->json();

            $googleUser = Http::withToken($tokenResponse['access_token'])
                ->get('https://www.googleapis.com/oauth2/v3/userinfo')
                ->throw()
                ->json();
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route($loginRoute)->withErrors([
                'email' => 'Login Google gagal. Periksa konfigurasi Google OAuth.',
            ]);
        }

        if (empty($googleUser['email'])) {
            return redirect()->route($loginRoute)->withErrors([
                'email' => 'Akun Google tidak mengirim alamat email.',
            ]);
        }

        $googleEmail = strtolower((string) $googleUser['email']);
        $googleRole = in_array($googleEmail, self::GOOGLE_ADMIN_EMAILS, true) ? 'admin' : $requestedGuard;

        $user = User::where('email', $googleEmail)->first();

        if (! $user && $mode !== 'register' && ! in_array($googleEmail, self::GOOGLE_ADMIN_EMAILS, true)) {
            return redirect()->route($requestedGuard === 'cashier' ? 'cashier.login.form' : 'login')->withErrors([
                'email' => 'Akun belum terdaftar. Silakan daftar dengan Google terlebih dahulu.',
            ]);
        }

        if (! $user) {
            $user = User::create([
                'name' => $googleUser['name'] ?? 'Admin',
                'email' => $googleEmail,
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(32)),
                'role' => $googleRole === 'cashier' ? 'cashier' : 'admin',
            ]);
        } elseif (in_array($googleEmail, self::GOOGLE_ADMIN_EMAILS, true) && $user->role !== 'admin') {
            $user->update(['role' => 'admin']);
        }

        if ($requestedGuard === 'cashier' && $user->role !== 'cashier') {
            return redirect()->route('cashier.login.form')->withErrors([
                'email' => 'Akun Google ini bukan akun kasir.',
            ]);
        }

        if ($requestedGuard === 'admin' && $user->role !== 'admin') {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Google ini bukan akun admin.',
            ]);
        }

        $guard = $user->role === 'cashier' ? 'cashier' : 'admin';
        Auth::guard($guard)->login($user, true);
        Auth::shouldUse($guard);
        $request->session()->regenerate();

        return redirect()->route($user->role === 'cashier' ? 'cashier.index' : 'dashboard');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        Auth::guard('admin')->login($user);
        Auth::shouldUse('admin');
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            Auth::shouldUse('admin');
            $request->session()->regenerate();

            if (Auth::guard('admin')->user()->role !== 'admin') {
                Auth::guard('admin')->logout();

                return back()
                    ->withErrors(['email' => 'Akun ini bukan akun admin.'])
                    ->onlyInput('email');
            }

            return redirect()->route('dashboard');
        }

        return back()
            ->withErrors(['email' => 'Email atau password tidak sesuai.'])
            ->onlyInput('email');
    }

    public function cashierLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('cashier')->attempt($credentials, $request->boolean('remember'))) {
            Auth::shouldUse('cashier');
            $request->session()->regenerate();

            if (Auth::guard('cashier')->user()->role !== 'cashier') {
                Auth::guard('cashier')->logout();

                return back()
                    ->withErrors(['email' => 'Akun ini bukan akun kasir.'])
                    ->onlyInput('email');
            }

            return redirect()->route('cashier.index');
        }

        return back()
            ->withErrors(['email' => 'Email atau password kasir tidak sesuai.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $guard = $request->query('redirect') === 'cashier' ? 'cashier' : 'admin';
        $user = Auth::guard($guard)->user() ?? Auth::user();
        $loginRoute = $request->query('redirect') === 'cashier' || $user?->role === 'cashier'
            ? 'cashier.login.form'
            : 'login';

        Auth::guard($guard)->logout();

        $request->session()->regenerateToken();

        return redirect()->route($loginRoute);
    }

    private function googleRedirectUri(): ?string
    {
        return config('services.google.redirect') ?: route('login.google.callback');
    }

    private function loginRouteForGuard(?string $guard): string
    {
        return $guard === 'cashier' ? 'cashier.login.form' : 'login';
    }
}
