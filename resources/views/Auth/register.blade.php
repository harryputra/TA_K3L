<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register Sistem K3L</title>
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
                <div class="w-full max-w-3xl rounded-[2rem] bg-white p-8 shadow-2xl sm:p-10">
                    <h2 class="text-3xl font-bold text-slate-900">Register Your Account</h2>
                    <p class="mt-3 text-base font-medium leading-7 text-slate-400">
                        Lengkapi data berikut untuk membuat akun pengguna baru.
                    </p>

                    @if ($errors->any())
                        <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('register.store') }}" method="POST" class="mt-8 flex flex-col gap-4">
                        @csrf

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="name" class="mb-2 block text-sm font-bold text-slate-900">Nama Lengkap</label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-blue-700 focus:ring-4 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="username" class="mb-2 block text-sm font-bold text-slate-900">Username</label>
                                <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder="Username"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-blue-700 focus:ring-4 focus:ring-blue-100">
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="email" class="mb-2 block text-sm font-bold text-slate-900">Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email aktif"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-blue-700 focus:ring-4 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="phone" class="mb-2 block text-sm font-bold text-slate-900">No. HP</label>
                                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-blue-700 focus:ring-4 focus:ring-blue-100">
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="password" class="mb-2 block text-sm font-bold text-slate-900">Password</label>
                                <input id="password" type="password" name="password" placeholder="Minimal 8 karakter"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-blue-700 focus:ring-4 focus:ring-blue-100">
                            </div>
                            <div>
                                <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-900">Konfirmasi Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Ulangi password"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-blue-700 focus:ring-4 focus:ring-blue-100">
                            </div>
                        </div>

                        <label for="terms" class="flex items-start gap-3 text-sm font-semibold text-slate-500">
                            <input id="terms" type="checkbox" checked disabled
                                class="mt-1 h-4 w-4 rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                            Dengan mendaftar, Anda menyetujui penggunaan akun untuk pelaporan dan pemantauan K3L kampus.
                        </label>

                        <button type="submit"
                            class="mt-2 rounded-full bg-blue-700 px-4 py-4 text-sm font-bold text-white transition hover:bg-blue-800">
                            Register
                        </button>
                    </form>

                    <div class="mt-8 flex items-center justify-between gap-5">
                        <span class="h-px w-full bg-slate-200"></span>
                        <span class="min-w-fit text-sm font-semibold text-slate-400">Sudah Punya Akun?</span>
                        <span class="h-px w-full bg-slate-200"></span>
                    </div>

                    <div class="mt-6 text-center text-sm text-slate-500">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-semibold text-slate-900 hover:underline">Login di sini</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>

</html>
