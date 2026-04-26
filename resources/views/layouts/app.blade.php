<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi K3L')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-stone-100 text-slate-900">
    <div class="relative isolate min-h-screen overflow-hidden">
        <div class="absolute inset-x-0 top-0 -z-10 h-72 bg-[radial-gradient(circle_at_top_left,_rgba(14,116,144,0.18),_transparent_40%),radial-gradient(circle_at_top_right,_rgba(245,158,11,0.18),_transparent_35%)]"></div>

        <header class="border-b border-white/80 bg-white/85 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-cyan-700">K3L Campus</p>
                    <h1 class="text-lg font-semibold text-slate-900">Sistem Informasi Keselamatan, Kesehatan Kerja, dan Lingkungan</h1>
                </div>

                <nav class="flex items-center gap-3 text-sm">
                    @auth
                        @php($dashboardRoute = route(auth()->user()->dashboardRouteName()))

                        <a href="{{ $dashboardRoute }}" class="rounded-full px-4 py-2 text-slate-700 transition hover:bg-slate-100 hover:text-slate-950">
                            Dashboard
                        </a>

                        @if (auth()->user()->isMahasiswa())
                            <a href="{{ route('user.incidents.index') }}" class="rounded-full px-4 py-2 text-slate-700 transition hover:bg-slate-100 hover:text-slate-950">
                                Laporan Insiden
                            </a>
                            <a href="{{ route('user.incidents.create') }}" class="rounded-full bg-cyan-700 px-4 py-2 font-medium text-white transition hover:bg-cyan-800">
                                Buat Laporan
                            </a>
                        @endif

                        @if (auth()->user()->isSatgas())
                            <a href="{{ route('satgas.incidents.index') }}" class="rounded-full px-4 py-2 text-slate-700 transition hover:bg-slate-100 hover:text-slate-950">
                                Review Insiden
                            </a>
                        @endif

                        @auth
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="rounded-full border border-slate-300 px-4 py-2 text-slate-700 transition hover:bg-slate-100 hover:text-slate-950">
                                    Logout
                                </button>
                            </form>
                        @endauth
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-8">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
    @include('partials.delete-confirm-modal')
    @stack('scripts')
</body>
</html>
