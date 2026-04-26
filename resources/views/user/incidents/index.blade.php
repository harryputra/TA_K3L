@extends('user.layouts.app')

@section('title', 'Laporan Insiden Saya')

@section('page')
    @php
        $statusSummary = [
            [
                'label' => 'Total Laporan',
                'value' => $summaryCounts['total_reports'],
                'hint' => 'seluruh laporan yang pernah dikirim',
                'color' => 'text-[var(--primary-color)]',
                'icon' => 'description',
            ],
            [
                'label' => 'Perlu Review',
                'value' => $summaryCounts['submitted_reports'],
                'hint' => 'sedang menunggu Satgas',
                'color' => 'text-[var(--yellow)]',
                'icon' => 'schedule',
            ],
            [
                'label' => 'Selesai',
                'value' => $summaryCounts['closed_reports'],
                'hint' => 'sudah ditutup',
                'color' => 'text-[var(--green)]',
                'icon' => 'task_alt',
            ],
        ];

        $statusBadge = fn (string $status): string => match ($status) {
            'submitted' => 'bg-amber-100 text-amber-800',
            'verified' => 'bg-emerald-100 text-emerald-800',
            'investigating' => 'bg-sky-100 text-sky-800',
            'closed' => 'bg-slate-200 text-slate-700',
            'rejected' => 'bg-rose-100 text-rose-700',
            default => 'bg-slate-100 text-slate-600',
        };
    @endphp

    <main class="w-full bg-[#f6f8fc] px-4 pb-12 pt-34 lg:px-6 xl:px-8">
        <div class="mx-auto flex w-full max-w-[1600px] flex-col gap-6">
            <section class="rounded-[1.4rem] bg-white/95 p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-7">
                @include('partials.flash')
                <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Riwayat Pelaporan</p>
                        <h2 class="mt-2 text-3xl font-bold text-slate-900">Laporan Insiden Saya</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                            Pantau seluruh laporan yang pernah Anda kirim, status prosesnya, dan buka detail untuk melihat perkembangan verifikasi.
                        </p>
                    </div>

                    <a href="{{ route('user.incidents.create') }}"
                        class="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                        Buat Laporan Baru
                    </a>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-3">
                @foreach ($statusSummary as $item)
                    <div class="rounded-[1.15rem] bg-white/95 px-5 py-5 shadow-[0_12px_30px_rgba(15,23,42,0.06)] ring-1 ring-[var(--primary-color)]/8">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $item['label'] }}</p>
                                <p class="mt-3 text-4xl font-bold text-slate-900">{{ $item['value'] }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $item['hint'] }}</p>
                            </div>
                            <span class="material-symbols-outlined text-4xl {{ $item['color'] }}">{{ $item['icon'] }}</span>
                        </div>
                    </div>
                @endforeach
            </section>

            <section class="overflow-hidden rounded-[1.4rem] bg-white/95 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8">
                <div class="border-b border-slate-100 px-6 py-5">
                    <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Workspace</p>
                            <h3 class="mt-2 text-2xl font-bold text-slate-900">Daftar laporan yang bisa Anda pantau</h3>
                        </div>
                        <p class="max-w-xl text-sm leading-7 text-slate-500">
                            Buka detail untuk melihat kronologi lengkap, riwayat perubahan status, dan lampiran pendukung tiap laporan.
                        </p>
                    </div>
                </div>
                <form action="{{ route('user.incidents.index') }}" method="GET" data-auto-submit-form data-live-submit
                    data-live-target="[data-live-region='user-incidents-list']"
                    class="grid gap-4 border-b border-slate-100 px-6 py-5 lg:grid-cols-[minmax(0,1fr)_220px]">
                    <label class="block">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Cari Laporan</span>
                        <input type="search" name="q" value="{{ $selectedQuery ?? '' }}"
                            placeholder="No laporan, judul, kategori, atau lokasi"
                            class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                    </label>
                    <label class="block">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</span>
                        <select name="status"
                            class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
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
                <div data-live-region="user-incidents-list">
                    <div class="grid gap-4 p-4 lg:p-5">
                        @forelse ($reports as $report)
                            <article class="rounded-[1rem] border border-slate-200 bg-[#f8fbff] px-5 py-5 transition hover:bg-white hover:shadow-[0_10px_25px_rgba(15,23,42,0.06)]">
                                <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                    <div class="space-y-4">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <span class="inline-flex rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white">
                                                {{ $report->report_number }}
                                            </span>
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $statusBadge($report->status) }}">
                                                {{ str_replace('_', ' ', $report->status) }}
                                            </span>
                                        </div>

                                        <div>
                                            <h4 class="text-2xl font-bold text-slate-900">{{ $report->title }}</h4>
                                            <p class="mt-2 text-sm uppercase tracking-[0.2em] text-slate-400">
                                                {{ optional($report->created_at)->format('d M Y H:i') }} WIB
                                            </p>
                                        </div>

                                        <div class="grid gap-4 sm:grid-cols-3">
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Kategori</p>
                                                <p class="mt-2 text-sm font-semibold text-slate-800">{{ $report->category?->name ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Lokasi</p>
                                                <p class="mt-2 text-sm font-semibold text-slate-800">{{ $report->location?->name ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tanggal Kejadian</p>
                                                <p class="mt-2 text-sm font-semibold text-slate-800">{{ optional($report->incident_date)->format('d M Y') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center xl:justify-end">
                                        <a href="{{ route('user.incidents.show', $report) }}"
                                            class="inline-flex items-center gap-2 rounded-full border border-[var(--primary-color)]/15 bg-white px-5 py-3 text-sm font-bold text-[var(--primary-color)]">
                                            Detail Laporan
                                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-[1rem] border border-dashed border-slate-300 bg-[#f8fbff] px-6 py-12 text-center text-slate-500">
                                Belum ada laporan insiden yang dikirim.
                            </div>
                        @endforelse
                    </div>

                    <div data-live-pagination data-live-target="[data-live-region='user-incidents-list']" class="border-t border-slate-200 px-6 py-4">
                        @if (filled($selectedQuery ?? null) || filled($selectedStatus ?? null))
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <p class="text-sm text-slate-500">Riwayat laporan sedang difilter secara realtime.</p>
                                <a href="{{ route('user.incidents.index') }}" class="text-sm font-semibold text-[var(--primary-color)]">
                                    Reset Filter
                                </a>
                            </div>
                        @endif
                        {{ $reports->links() }}
                    </div>
                </div>
            </section>
        </div>
    </main>
@endsection
