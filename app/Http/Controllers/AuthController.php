<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('Auth.login');
    }

    public function showRegisterForm(): View
    {
        return view('Auth.register');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('login'))
                ->withErrors([
                    'login' => 'Kredensial tidak valid atau akun belum terdaftar.',
                ]);
        }

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = $request->user()->loadMissing('role');

        if (! $user->is_active) {
            Auth::logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'login' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Admin K3L.',
                ]);
        }

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        return redirect()->intended(route($user->dashboardRouteName()));
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated): User {
            $mahasiswaRole = Role::query()->where('code', 'mahasiswa')->firstOrFail();

            return User::query()->create([
                'role_id' => $mahasiswaRole->id,
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => $validated['password'],
                'is_active' => true,
            ]);
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('user.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
