@extends('user.layouts.app')

@section('title', 'Status Laporan')

@section('page')
    @php
        $statusBadge = fn (string $status): string => match ($status) {
            'submitted' => 'bg-amber-100 text-amber-800',
            'verified' => 'bg-emerald-100 text-emerald-800',
            'investigating' => 'bg-sky-100 text-sky-800',
            'resolved' => 'bg-indigo-100 text-indigo-700',
            'closed' => 'bg-slate-200 text-slate-700',
            'rejected' => 'bg-rose-100 text-rose-700',
            default => 'bg-slate-100 text-slate-600',
        };
    @endphp

    <header id="header" class="relative flex h-135 w-full flex-col items-center justify-center gap-4 px-6 pt-30">
        <div class="pointer-events-none absolute inset-x-0 bottom-8 mx-auto h-28 w-[82%] rounded-full bg-white/12 blur-3xl"></div>
        <div class="relative z-1 flex max-w-6xl flex-col items-center">
            <span class="inline-flex rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white/90">Portal Operasional K3L</span>
            <h1 class="mt-6 text-center text-5xl font-bold text-white lg:text-7xl">Status Pelaporan</h1>
            <p class="max-w-6xl px-4 pt-2 text-center text-lg text-white/90 lg:text-2xl">
                Masukkan nomor laporan, email, atau nomor WhatsApp pelapor untuk melihat perkembangan verifikasi dan tindak lanjut.
            </p>
        </div>
    </header>

    <main class="w-full bg-[#f6f8fc] pt-20 pb-12">
        <div class="mx-auto flex w-full max-w-[1600px] flex-col gap-6 px-4 lg:px-6 xl:px-8">
            <section class="section-shell overflow-hidden rounded-[2rem] p-5 shadow-[0_22px_50px_rgba(15,23,42,0.1)] ring-1 ring-white/85 lg:p-6">
                <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Papan Status</p>
                        <h2 class="mt-2 text-3xl font-bold text-slate-900">Cek posisi laporan secara cepat</h2>
                        <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600">
                            Pencarian status hanya menampilkan laporan yang cocok dengan nomor laporan atau kontak pelapor yang dimasukkan.
                        </p>
                    </div>
                    <a href="{{ route('user.incidents.create') }}"
                        class="inline-flex min-h-12 items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white shadow-[0_15px_30px_rgba(10,77,179,0.18)] transition hover:-translate-y-1">
                        Buat Laporan Baru
                    </a>
                </div>

                <form action="{{ route('user.incidents.status') }}" method="GET" data-auto-submit-form data-live-submit
                    data-live-target="[data-live-region='user-incidents-status-updates']"
                    class="mt-6 grid gap-4 rounded-[1.4rem] bg-white/72 p-4 ring-1 ring-[var(--primary-color)]/8 lg:grid-cols-[minmax(0,1fr)_220px]">
                    <label class="block">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Cari Laporan</span>
                        <input type="search" name="q" value="{{ $selectedQuery ?? '' }}"
                            placeholder="No laporan, email, atau no WhatsApp"
                            class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)]">
                    </label>
                    <label class="block">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</span>
                        <select name="status"
                            class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)]">
                            <option value="">Semua Status</option>
                            <option value="submitted" @selected(($selectedStatus ?? '') === 'submitted')>Submitted</option>
                            <option value="verified" @selected(($selectedStatus ?? '') === 'verified')>Verified</option>
                            <option value="investigating" @selected(($selectedStatus ?? '') === 'investigating')>Investigating</option>
                            <option value="resolved" @selected(($selectedStatus ?? '') === 'resolved')>Resolved</option>
                            <option value="closed" @selected(($selectedStatus ?? '') === 'closed')>Closed</option>
                            <option value="rejected" @selected(($selectedStatus ?? '') === 'rejected')>Rejected</option>
                        </select>
                    </label>
                </form>

                <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                @foreach ($statusBoard as $item)
                    <article class="flex min-h-[245px] flex-col rounded-[1.5rem] bg-white/92 px-5 py-5 shadow-[0_14px_32px_rgba(15,23,42,0.07)] ring-1 ring-[var(--primary-color)]/8">
                        <div class="flex items-start justify-between gap-3">
                            <p class="max-w-[11rem] text-sm font-semibold uppercase tracking-[0.28em] text-slate-500">{{ $item['title'] }}</p>
                        </div>
                        <p class="mt-5 text-5xl font-bold leading-none text-slate-900">{{ $item['count'] }}</p>
                        <p class="mt-6 flex-1 text-sm leading-8 text-slate-600">{{ $item['description'] }}</p>
                    </article>
                @endforeach
                </div>
            </section>

            <section data-live-region="user-incidents-status-updates" class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
                <div class="rounded-[1.4rem] bg-white/95 p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-7">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Ringkasan Progress</p>
                            <h2 class="mt-2 text-2xl font-bold text-slate-900">Distribusi status laporan</h2>
                        </div>
                        <span class="text-sm font-semibold text-slate-500">{{ $reports->count() }} total laporan</span>
                    </div>

                    <div class="mt-6 space-y-5">
                        @foreach ($statusBoard as $item)
                            @php
                                $totalReports = max($reports->count(), 1);
                                $percentage = min(100, (int) round(($item['count'] / $totalReports) * 100));
                            @endphp
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <span class="h-3 w-3 rounded-full {{ str_contains($item['accent'], 'amber') ? 'bg-amber-500' : (str_contains($item['accent'], 'emerald') ? 'bg-emerald-500' : (str_contains($item['accent'], 'sky') ? 'bg-sky-500' : (str_contains($item['accent'], 'indigo') ? 'bg-indigo-500' : 'bg-slate-500'))) }}"></span>
                                        <span class="text-sm font-semibold text-slate-700">{{ $item['title'] }}</span>
                                    </div>
                                    <span class="text-sm font-bold text-slate-900">{{ $percentage }}%</span>
                                </div>
                                <div class="h-4 rounded-full bg-slate-100">
                                    <div class="h-full rounded-full {{ str_contains($item['accent'], 'amber') ? 'bg-amber-500' : (str_contains($item['accent'], 'emerald') ? 'bg-emerald-500' : (str_contains($item['accent'], 'sky') ? 'bg-sky-500' : (str_contains($item['accent'], 'indigo') ? 'bg-indigo-500' : 'bg-slate-500'))) }}" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[1.4rem] bg-white/95 p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-7">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Update Terbaru</p>
                            <h2 class="mt-2 text-2xl font-bold text-slate-900">Hasil pencarian laporan</h2>
                        </div>
                        @if (filled($selectedQuery ?? null) || filled($selectedStatus ?? null))
                            <a href="{{ route('user.incidents.status') }}" class="text-sm font-semibold text-[var(--primary-color)]">
                                Reset Filter
                            </a>
                        @endif
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse ($recentStatusReports as $report)
                            <a href="{{ route('user.incidents.show', $report) }}"
                                class="block rounded-[1rem] border border-slate-200 bg-[#f8fbff] px-5 py-4 transition hover:bg-white hover:shadow-[0_8px_22px_rgba(15,23,42,0.05)]">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="inline-flex rounded-full bg-slate-900 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-white">
                                        {{ $report->report_number }}
                                    </span>
                                    <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-wide {{ $statusBadge($report->status) }}">
                                        {{ str_replace('_', ' ', $report->status) }}
                                    </span>
                                </div>
                                <h3 class="mt-3 text-lg font-bold text-slate-900">{{ $report->title }}</h3>
                                <p class="mt-2 text-sm text-slate-500">
                                    {{ $report->category?->name ?? '-' }} • {{ $report->location?->name ?? '-' }}
                                </p>
                            </a>
                        @empty
                            <div class="rounded-[1rem] border border-dashed border-slate-300 bg-[#f8fbff] px-5 py-10 text-center text-slate-500">
                                Belum ada laporan yang bisa dipantau.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </main>
@endsection
