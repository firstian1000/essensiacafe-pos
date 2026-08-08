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
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('dashboard');
            }

            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Session kasir sudah keluar. Silakan login admin.',
            ]);
        }

        return view('admin.auth.login');
    }

    public function showCashierLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'cashier') {
                return redirect()->route('cashier.index');
            }

            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('cashier.login.form')->withErrors([
                'email' => 'Session admin sudah keluar. Silakan login kasir.',
            ]);
        }

        return view('cashier.auth.login');
    }

    public function redirectToGoogle()
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret') || ! config('services.google.redirect')) {
            return back()->withErrors([
                'email' => 'Login Google belum aktif. Isi GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, dan GOOGLE_REDIRECT_URI.',
            ]);
        }

        $state = Str::random(40);
        session(['google_oauth_state' => $state]);

        $query = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
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
        if (! hash_equals((string) session('google_oauth_state'), (string) $request->query('state'))) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi login Google tidak valid. Silakan coba lagi.',
            ]);
        }

        $request->session()->forget('google_oauth_state');

        if ($request->filled('error')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Login Google dibatalkan atau tidak diizinkan.',
            ]);
        }

        if (! $request->filled('code')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Kode login Google tidak ditemukan. Silakan coba lagi.',
            ]);
        }

        try {
            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'code' => $request->query('code'),
                'grant_type' => 'authorization_code',
                'redirect_uri' => config('services.google.redirect'),
            ])->throw()->json();

            $googleUser = Http::withToken($tokenResponse['access_token'])
                ->get('https://www.googleapis.com/oauth2/v3/userinfo')
                ->throw()
                ->json();
        } catch (\Throwable $exception) {
            return redirect()->route('login')->withErrors([
                'email' => 'Login Google gagal. Periksa konfigurasi Google OAuth.',
            ]);
        }

        if (empty($googleUser['email'])) {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Google tidak mengirim alamat email.',
            ]);
        }

        $user = User::firstOrCreate(
            ['email' => $googleUser['email']],
            [
                'name' => $googleUser['name'] ?? 'Admin',
                'password' => Hash::make(Str::random(32)),
            ],
        );

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route($user->role === 'cashier' ? 'cashier.index' : 'dashboard');
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

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
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role !== 'cashier') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

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
        $loginRoute = $request->query('redirect') === 'cashier' || Auth::user()?->role === 'cashier'
            ? 'cashier.login.form'
            : 'login';

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($loginRoute);
    }
}
