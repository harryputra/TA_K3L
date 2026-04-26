<div id="user-floating-navbar" class="fixed inset-x-0 top-0 z-[99] flex w-full justify-center px-4 pt-4 transition-all duration-300 ease-out translate-y-0 opacity-100">
    <nav
        class="frosted-panel pointer-events-auto flex w-full max-w-[1360px] flex-wrap items-center justify-between gap-4 rounded-[2rem] px-4 py-3 sm:px-5">
        <div class="flex items-center gap-4">
            <div class="logo flex h-15 w-15 items-center justify-center rounded-full bg-white p-3">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-10 w-9">
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-[var(--primary-color)]/70">Portal K3L</p>
                <h2 class="text-lg font-bold text-[var(--primary-color)]">SIAGA POLMAN</h2>
            </div>
        </div>

        <div class="flex h-fit flex-wrap items-center gap-2 rounded-2xl bg-[var(--blue-low-opacity)] px-2 py-2">
            <div>
                <a href="{{ route('user.dashboard') }}"
                    class="flex flex-row items-center gap-2 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('user.dashboard') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }}">
                    <span class="material-symbols-outlined text-[var(--primary-color)]">
                        home_app_logo
                    </span>
                    <h6 class="text-[var(--primary-color)] font-bold">Home</h6>
                </a>
            </div>

            <div class="w-fit">
                <a href="{{ route('user.incidents.status') }}"
                    class="flex flex-row items-center gap-2 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('user.incidents.*') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }}">
                    <span class="material-symbols-outlined text-[var(--primary-color)]">
                        timeline
                    </span>
                    <h6 class="text-[var(--primary-color)] font-bold">Status Pelaporan</h6>
                </a>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3">
            <details class="group relative">
                <summary
                    class="list-none [&::-webkit-details-marker]:hidden flex h-12 w-12 items-center justify-center rounded-xl cursor-pointer transition hover:bg-[var(--blue-low-opacity)]">
                    <span class="material-symbols-outlined text-[var(--primary-color)]">
                        more_vert
                    </span>
                </summary>

                <div
                    class="ambient-card absolute right-0 top-16 w-64 rounded-3xl p-3 shadow-all ring-1 ring-slate-100">
                    <div class="px-3 py-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Menu Cepat</p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <a href="{{ route('user.profile.show') }}"
                            class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)] {{ request()->routeIs('user.profile.*') ? 'bg-[var(--blue-low-opacity)] text-[var(--primary-color)]' : '' }}">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">account_circle</span>
                            Profil Saya
                        </a>

                        <a href="{{ route('user.knowledge.index') }}"
                            class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)] {{ request()->routeIs('user.knowledge.*') ? 'bg-[var(--blue-low-opacity)] text-[var(--primary-color)]' : '' }}">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">book_5</span>
                            Materi K3
                        </a>

                        <a href="{{ route('user.emergency.index') }}"
                            class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)] {{ request()->routeIs('user.emergency.*') ? 'bg-[var(--blue-low-opacity)] text-[var(--primary-color)]' : '' }}">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">emergency_home</span>
                            Pusat Darurat
                        </a>

                        <a href="{{ route('user.hazards.create') }}"
                            class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)] {{ request()->routeIs('user.hazards.*') ? 'bg-[var(--blue-low-opacity)] text-[var(--primary-color)]' : '' }}">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">warning</span>
                            Potensi Bahaya
                        </a>

                        <a href="{{ route('user.activities.index') }}"
                            class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)] {{ request()->routeIs('user.activities.*') ? 'bg-[var(--blue-low-opacity)] text-[var(--primary-color)]' : '' }}">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">notifications</span>
                            Aktivitas Saya
                        </a>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-rose-600 transition hover:bg-rose-50">
                                <span class="material-symbols-outlined">logout</span>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </details>

            <a href="{{ route('user.activities.index') }}" class="relative flex h-12 w-12 items-center justify-center rounded-xl bg-white/70 p-2 transition hover:bg-[var(--blue-low-opacity)]">
                <span class="material-symbols-outlined text-[var(--primary-color)] cursor-pointer">
                    notifications_unread
                </span>
                @if (($unreadActivityCount ?? 0) > 0)
                    <span class="absolute -right-1 -top-1 inline-flex min-h-6 min-w-6 items-center justify-center rounded-full bg-[var(--red)] px-1 text-xs font-bold text-white">
                        {{ $unreadActivityCount > 99 ? '99+' : $unreadActivityCount }}
                    </span>
                @endif
            </a>
            <span class="hidden h-13 w-px rounded-full bg-slate-300 sm:block"></span>
            <button
                class="flex h-15 w-auto cursor-pointer items-center gap-3 rounded-full bg-[var(--primary-color)] px-3 py-3 shadow-[0_15px_30px_rgba(10,77,179,0.24)]">
                <span
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white font-bold">{{ $initials !== '' ? $initials : 'M' }}</span>
                <div class="text-left">
                    <h6 class="font-bold text-white">{{ $user->name }}</h6>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Mahasiswa</p>
                </div>
            </button>
        </div>
    </nav>
</div>

@push('scripts')
    <script>
        (() => {
            const navbar = document.getElementById('user-floating-navbar');

            if (!navbar) {
                return;
            }

            let lastY = window.scrollY;
            let idleTimer = null;
            let isHidden = false;
            const scrollThreshold = 12;
            const topRevealOffset = 32;
            const idleDelay = 320;

            const showNavbar = () => {
                if (!isHidden) {
                    return;
                }

                navbar.classList.remove('-translate-y-[140%]', 'opacity-0');
                navbar.classList.add('translate-y-0', 'opacity-100');
                isHidden = false;
            };

            const hideNavbar = () => {
                if (isHidden) {
                    return;
                }

                navbar.classList.remove('translate-y-0', 'opacity-100');
                navbar.classList.add('-translate-y-[140%]', 'opacity-0');
                isHidden = true;
            };

            const armIdleReveal = () => {
                window.clearTimeout(idleTimer);
                idleTimer = window.setTimeout(() => {
                    showNavbar();
                }, idleDelay);
            };

            navbar.classList.add('translate-y-0', 'opacity-100');
            armIdleReveal();

            window.addEventListener('scroll', () => {
                const currentY = window.scrollY;
                const delta = currentY - lastY;

                if (currentY <= topRevealOffset) {
                    showNavbar();
                    lastY = currentY;
                    armIdleReveal();
                    return;
                }

                if (delta > scrollThreshold) {
                    hideNavbar();
                } else if (delta < -scrollThreshold) {
                    showNavbar();
                }

                lastY = currentY;
                armIdleReveal();
            }, { passive: true });

            ['mouseenter', 'focusin', 'touchstart'].forEach((eventName) => {
                navbar.addEventListener(eventName, () => {
                    showNavbar();
                    armIdleReveal();
                }, { passive: true });
            });
        })();
    </script>
@endpush
