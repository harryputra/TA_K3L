<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Sistem K3L</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        .auth-hero {
            background: url('/img/background.jpeg') no-repeat center center fixed;
            background-size: cover;
        }

        .auth-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to top right, rgba(2, 21, 142, 0.4), rgba(170, 102, 23, 0.4));
            z-index: 0;
        }
    </style>
</head>

<body class="bg-slate-100 text-slate-900">
    <div class="auth-hero relative min-h-screen overflow-hidden">
        <div class="relative z-10 grid min-h-screen lg:grid-cols-5">
            <section class="col-span-3 hidden px-10 py-12 text-white lg:flex lg:flex-col lg:justify-between">
                <div class="w-fit rounded-3xl border border-white/15 bg-white p-6">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo K3L" class="h-12 w-11">
                </div>
                <div>
                    <h1 class="mt-6 max-w-2xl text-5xl font-bold leading-tight">Utamakan Keselamatan, Mulai Dari Sini
                    </h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-100/90">
                        Setiap laporan yang Anda buat adalah langkah nyata dalam melindungi nyawa rekan kerja dan
                        memastikan semua pulang dengan selamat.
                    </p>
                    <span class="h-10 w-full bg-slate-200"></span>
                </div>
            </section>

            <section class="col-span-5 flex items-center justify-center p-6 sm:p-8 lg:col-span-2">
                <div class="w-full max-w-2xl rounded-[2rem] bg-white p-8 shadow-2xl sm:p-10">
                    <h2 class="text-3xl font-bold text-slate-900">Welcome Back</h2>
                    <p class="mt-3 text-base font-medium leading-7 text-slate-400">
                        Masuk untuk mengakses dashboard K3L sesuai peran Anda.
                    </p>

                    @if ($errors->any())
                        <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.attempt') }}" method="POST" class="mt-8 flex flex-col gap-4">
                        @csrf

                        <div>
                            <label for="login" class="mb-2 block text-sm font-bold text-slate-900">Email atau
                                Username</label>
                            <input id="login" name="login" type="text" value="{{ old('login') }}"
                                autocomplete="username" placeholder="Masukkan email atau username"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-blue-700 focus:ring-4 focus:ring-blue-100">
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-bold text-slate-900">Password</label>
                            <input id="password" name="password" type="password" autocomplete="current-password"
                                placeholder="Masukkan password"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-blue-700 focus:ring-4 focus:ring-blue-100">
                        </div>

                        <div
                            class="flex flex-col gap-3 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                            <label for="remember" class="flex items-center gap-2 font-semibold">
                                <input id="remember" type="checkbox" name="remember" value="1"
                                    class="h-4 w-4 rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                                Remember Me
                            </label>
                            <span class="text-slate-400">Hubungi admin jika lupa akses akun.</span>
                        </div>

                        <button type="submit"
                            class="mt-2 rounded-full bg-blue-700 px-4 py-4 text-sm font-bold text-white transition hover:bg-blue-800">
                            Login
                        </button>
                    </form>

                    <div class="mt-8 flex items-center justify-between gap-5">
                        <span class="h-px w-full bg-slate-200"></span>
                        <span class="min-w-fit text-sm font-semibold text-slate-400">Akses Mahasiswa Baru</span>
                        <span class="h-px w-full bg-slate-200"></span>
                    </div>

                    <div class="mt-6 text-center text-sm text-slate-500">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="font-semibold text-slate-900 hover:underline">Daftar di
                            sini</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>

</html>
