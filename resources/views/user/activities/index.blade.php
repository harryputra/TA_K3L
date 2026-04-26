@extends('user.layouts.app')

@section('title', 'Aktivitas Saya')

@section('page')
    <header id="header" class="relative flex h-135 w-full flex-col items-center justify-center gap-4 px-6 pt-30">
        <div class="pointer-events-none absolute inset-x-0 bottom-8 mx-auto h-28 w-[82%] rounded-full bg-white/12 blur-3xl"></div>
        <div class="relative z-1 flex max-w-6xl flex-col items-center">
            <span class="inline-flex rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white/90">Portal Operasional K3L</span>
            <h1 class="mt-6 text-center text-5xl font-bold text-white lg:text-7xl">Aktivitas Saya</h1>
            <p class="max-w-6xl px-4 pt-2 text-center text-lg text-white/90 lg:text-2xl">
                Pantau pembaruan penting dari laporan dan hazard Anda, termasuk status terbaru, tindak lanjut sistem, dan riwayat aktivitas akun.
            </p>
        </div>
    </header>

    <main class="w-full bg-white pb-14 pt-15">
        <section class="mx-auto flex w-full max-w-[1600px] flex-col gap-6 px-4 lg:px-8">

            <section class="grid gap-4 md:grid-cols-4">
                <article class="bg-white rounded-[1.4rem] px-6 py-6 shadow-[0_16px_35px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Total</p>
                    <p class="mt-3 text-4xl font-bold text-[var(--primary-color)]">{{ $summary['total'] }}</p>
                </article>
                <article class="bg-white rounded-[1.4rem] px-6 py-6 shadow-[0_16px_35px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Belum Dibaca</p>
                    <p class="mt-3 text-4xl font-bold text-[var(--primary-color)]">{{ $summary['unread'] }}</p>
                </article>
                <article class="bg-white rounded-[1.4rem] px-6 py-6 shadow-[0_16px_35px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Insiden</p>
                    <p class="mt-3 text-4xl font-bold text-[var(--primary-color)]">{{ $summary['incident_related'] }}</p>
                </article>
                <article class="bg-white rounded-[1.4rem] px-6 py-6 shadow-[0_16px_35px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Hazard</p>
                    <p class="mt-3 text-4xl font-bold text-[var(--primary-color)]">{{ $summary['hazard_related'] }}</p>
                </article>
            </section>

            <div class="section-shell rounded-[1.8rem] px-8 py-8 shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
                <div class="mb-6 flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-[var(--primary-color)]">Riwayat Aktivitas</h2>
                        <p class="mt-2 text-sm leading-7 text-slate-500">Aktivitas terbaru akan muncul otomatis saat status insiden atau hazard berubah.</p>
                    </div>
                    <form action="{{ route('user.activities.read-all') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-bold text-white transition hover:opacity-90">
                            Tandai Semua Dibaca
                        </button>
                    </form>
                </div>

                <form action="{{ route('user.activities.index') }}" method="GET" data-auto-submit-form data-live-submit
                    data-live-target="[data-live-region='user-activities-list']"
                    class="mb-6 grid gap-4 rounded-[1.25rem] bg-white/90 p-4 ring-1 ring-slate-200 lg:grid-cols-[minmax(0,1fr)_220px_220px]">
                    <label class="block">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Cari Aktivitas</span>
                        <input type="search" name="q" value="{{ $selectedQuery ?? '' }}"
                            placeholder="Judul, deskripsi, atau tipe aktivitas"
                            class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                    </label>
                    <label class="block">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status Baca</span>
                        <select name="read_status"
                            class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                            <option value="all" @selected(($selectedReadStatus ?? 'all') === 'all')>Semua</option>
                            <option value="unread" @selected(($selectedReadStatus ?? 'all') === 'unread')>Belum Dibaca</option>
                            <option value="read" @selected(($selectedReadStatus ?? 'all') === 'read')>Sudah Dibaca</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Jenis</span>
                        <select name="type"
                            class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                            <option value="all" @selected(($selectedType ?? 'all') === 'all')>Semua</option>
                            <option value="incident" @selected(($selectedType ?? 'all') === 'incident')>Insiden</option>
                            <option value="hazard" @selected(($selectedType ?? 'all') === 'hazard')>Hazard</option>
                            <option value="system" @selected(($selectedType ?? 'all') === 'system')>Sistem</option>
                        </select>
                    </label>
                </form>

                <div data-live-region="user-activities-list">
                    <div class="space-y-4">
                        @forelse ($activities as $activity)
                            <article class="rounded-[1.35rem] px-5 py-5 ring-1 transition {{ $activity->read_at ? 'bg-white/85 ring-slate-100' : 'bg-[#eef5ff] ring-[var(--primary-color)]/15 shadow-[0_14px_30px_rgba(10,77,179,0.08)]' }}">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-3">
                                            <h2 class="text-lg font-bold text-[var(--primary-color)]">{{ $activity->title }}</h2>
                                            @if ($activity->read_at === null)
                                                <span class="inline-flex rounded-full bg-[var(--yellow)]/15 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-[var(--orange)]">
                                                    Baru
                                                </span>
                                            @endif
                                        </div>
                                        <p class="mt-2 text-sm leading-7 text-slate-600">{{ $activity->description ?: '-' }}</p>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <p class="text-sm font-semibold text-slate-500">{{ optional($activity->occurred_at)->format('d M Y, H:i') ?? '-' }} WIB</p>
                                        <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400">{{ str_replace('_', ' ', $activity->type) }}</p>
                                        @if ($activity->read_at === null)
                                            <form action="{{ route('user.activities.read', $activity) }}" method="POST" class="mt-3">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center justify-center rounded-full border border-[var(--primary-color)] bg-white px-4 py-2 text-xs font-bold text-[var(--primary-color)] transition hover:bg-[var(--blue-low-opacity)]">
                                                    Tandai Dibaca
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-4 text-xs text-slate-500">
                                    <span>Pelaku: {{ $activity->actor?->name ?? 'Sistem' }}</span>
                                    <span>Subjek: {{ class_basename($activity->subject_type ?? 'Umum') }}</span>
                                    <span>Status baca: {{ $activity->read_at ? 'Sudah dibaca' : 'Belum dibaca' }}</span>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-[1.1rem] bg-[#f8fbff] px-5 py-10 text-center text-slate-500 ring-1 ring-slate-100">
                                Belum ada aktivitas yang tercatat untuk akun Anda.
                            </div>
                        @endforelse
                    </div>

                    <div data-live-pagination data-live-target="[data-live-region='user-activities-list']" class="mt-6 border-t border-slate-200 pt-6">
                        @if (filled($selectedQuery ?? null) || ($selectedReadStatus ?? 'all') !== 'all' || ($selectedType ?? 'all') !== 'all')
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <p class="text-sm text-slate-500">Daftar aktivitas sedang difilter secara realtime.</p>
                                <a href="{{ route('user.activities.index') }}" class="text-sm font-semibold text-[var(--primary-color)]">
                                    Reset Filter
                                </a>
                            </div>
                        @endif
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
