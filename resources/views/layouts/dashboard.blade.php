<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard K3L')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,500,0,0"
        rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        :root {
            --dashboard-primary: #0048a7;
            --dashboard-primary-soft: rgba(169, 191, 221, 0.28);
            --dashboard-orange: #ff6505;
            --dashboard-green: #00a42c;
            --dashboard-dark-green: #003e11;
            --dashboard-red: #d92d20;
            --dashboard-yellow: #fbbc05;
        }

        body {
            font-family: "Poppins", sans-serif;
        }

        .dashboard-hero {
            background: url('/img/background.jpeg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            overflow: hidden;
        }

        .dashboard-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(2, 21, 142, 0.82), rgba(170, 102, 23, 0.68));
        }

        .dashboard-shadow {
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.12);
        }

        .dashboard-glow {
            box-shadow: 0 0 18px rgba(0, 72, 167, 0.22);
        }
    </style>
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">
    @php
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user()->loadMissing('role');
        $nameParts = preg_split('/\s+/', trim($authUser->name)) ?: [];
        $initials = collect($nameParts)
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');
        $dashboardRoute = route($authUser->dashboardRouteName());
    @endphp

    <div class="min-h-screen">
        <div class="sticky top-0 z-50 px-4 pt-4 sm:px-6">
            <nav class="mx-auto flex w-full max-w-7xl flex-wrap items-center justify-between gap-4 rounded-[2rem] bg-white/95 px-4 py-3 shadow-lg backdrop-blur sm:px-6">
                <div class="flex items-center gap-4">
                    <div class="dashboard-glow flex h-14 w-14 items-center justify-center rounded-full bg-white p-3">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo K3L" class="h-9 w-8">
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[var(--dashboard-primary)]">SIAGA POLMAN</p>
                        <h1 class="text-sm font-bold text-slate-900 sm:text-base">Portal Dashboard K3L</h1>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 rounded-2xl bg-[var(--dashboard-primary-soft)] p-1.5">
                    <a href="{{ $dashboardRoute }}"
                        class="{{ request()->routeIs('*.dashboard') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }} flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-[var(--dashboard-primary)] transition">
                        <span class="material-symbols-outlined text-[20px]">home_app_logo</span>
                        Dashboard
                    </a>

                    @if ($authUser->isMahasiswa())
                        <a href="{{ route('user.incidents.index') }}"
                            class="{{ request()->routeIs('user.incidents.index', 'user.incidents.show') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }} flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-[var(--dashboard-primary)] transition">
                            <span class="material-symbols-outlined text-[20px]">docs</span>
                            Pelaporan
                        </a>
                        <a href="{{ route('user.incidents.create') }}"
                            class="{{ request()->routeIs('user.incidents.create') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }} flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-[var(--dashboard-primary)] transition">
                            <span class="material-symbols-outlined text-[20px]">edit_square</span>
                            Buat Laporan
                        </a>
                    @endif

                    @if ($authUser->isSatgas())
                        <a href="{{ route('satgas.incidents.index') }}"
                            class="{{ request()->routeIs('satgas.incidents.*') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }} flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-[var(--dashboard-primary)] transition">
                            <span class="material-symbols-outlined text-[20px]">fact_check</span>
                            Review Insiden
                        </a>
                        <a href="{{ route('satgas.hazards.index') }}"
                            class="{{ request()->routeIs('satgas.hazards.*') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }} flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-[var(--dashboard-primary)] transition">
                            <span class="material-symbols-outlined text-[20px]">warning</span>
                            Review Hazard
                        </a>
                    @endif

                    @if ($authUser->isAdmin())
                        <a href="{{ route('admin.locations.index') }}"
                            class="{{ request()->routeIs('admin.locations.*') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }} flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-[var(--dashboard-primary)] transition">
                            <span class="material-symbols-outlined text-[20px]">pin_drop</span>
                            Lokasi
                        </a>
                        <a href="{{ route('admin.incident-categories.index') }}"
                            class="{{ request()->routeIs('admin.incident-categories.*') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }} flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-[var(--dashboard-primary)] transition">
                            <span class="material-symbols-outlined text-[20px]">category</span>
                            Kategori
                        </a>
                        <a href="{{ route('admin.knowledge-articles.index') }}"
                            class="{{ request()->routeIs('admin.knowledge-articles.*', 'admin.knowledge-categories.*') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }} flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-[var(--dashboard-primary)] transition">
                            <span class="material-symbols-outlined text-[20px]">menu_book</span>
                            Knowledge
                        </a>
                        <a href="{{ route('admin.emergency-contacts.index') }}"
                            class="{{ request()->routeIs('admin.emergency-contacts.*', 'admin.emergency-response-steps.*', 'admin.first-aid-guides.*') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }} flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-[var(--dashboard-primary)] transition">
                            <span class="material-symbols-outlined text-[20px]">emergency_home</span>
                            Emergency
                        </a>
                        <a href="{{ route('admin.hazards.index') }}"
                            class="{{ request()->routeIs('admin.hazards.*') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }} flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-[var(--dashboard-primary)] transition">
                            <span class="material-symbols-outlined text-[20px]">report</span>
                            Hazard
                        </a>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="hidden h-10 w-px rounded-full bg-slate-200 sm:block"></div>
                    <div class="flex items-center gap-3 rounded-full bg-[var(--dashboard-primary)] px-3 py-2 text-white">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-sm font-bold">
                            {{ $initials !== '' ? $initials : 'U' }}
                        </span>
                        <div class="pr-1">
                            <p class="text-sm font-bold">{{ $authUser->name }}</p>
                            <p class="text-xs uppercase tracking-[0.18em] text-white/75">{{ $authUser->role?->name ?? 'Pengguna' }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                            <span class="material-symbols-outlined text-[20px]">logout</span>
                            Logout
                        </button>
                    </form>
                </div>
            </nav>
        </div>

        <header class="dashboard-hero -mt-24 flex min-h-[34rem] items-center justify-center px-6 pb-16 pt-36">
            <div class="relative z-10 mx-auto flex w-full max-w-7xl flex-col items-center text-center text-white">
                <p class="text-sm font-semibold uppercase tracking-[0.42em] text-white/80">@yield('hero_eyebrow', 'Dashboard')</p>
                <h2 class="mt-6 max-w-5xl text-4xl font-extrabold leading-tight sm:text-5xl lg:text-6xl">
                    @yield('hero_title', 'Pusat Pelaporan & Edukasi K3L')
                </h2>
                <p class="mt-5 max-w-4xl text-base leading-8 text-slate-100/90 sm:text-lg">
                    @yield('hero_description', 'Dashboard operasional K3L untuk mendukung pelaporan, verifikasi, dan pengelolaan tindak lanjut.')
                </p>
            </div>
        </header>

        <main class="-mt-16 pb-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">
                @include('partials.flash')
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>

</html>
