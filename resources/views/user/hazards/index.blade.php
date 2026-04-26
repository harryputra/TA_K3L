@extends('user.layouts.app')

@section('title', 'Status Hazard Report')

@section('page')
    @php
        $statusBadge = fn (string $status): string => match ($status) {
            'submitted' => 'bg-amber-100 text-amber-800',
            'reviewed' => 'bg-sky-100 text-sky-800',
            'resolved' => 'bg-emerald-100 text-emerald-800',
            default => 'bg-slate-100 text-slate-600',
        };
    @endphp

    <main class="w-full bg-white pb-14 pt-30">
        <section class="mx-auto flex w-full max-w-[1600px] flex-col gap-6 px-4 lg:px-8">
            <div class="rounded-[1.45rem] bg-white px-10 py-10 shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Hazard Report</p>
                <h1 class="mt-3 text-5xl font-bold text-[var(--primary-color)]">Riwayat Potensi Bahaya Saya</h1>
                <p class="mt-4 max-w-4xl text-lg font-semibold leading-9 text-slate-600">
                    Pantau status hazard report yang sudah Anda kirim, termasuk respons Satgas dan hasil penyelesaiannya.
                </p>
            </div>

            <form action="{{ route('user.hazards.index') }}" method="GET" data-auto-submit-form data-live-submit
                data-live-target="[data-live-region='user-hazards-table']"
                class="grid gap-4 rounded-[1.25rem] bg-white p-5 shadow-[0_12px_30px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:grid-cols-[minmax(0,1fr)_220px]">
                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Cari Hazard</span>
                    <input type="search" name="q" value="{{ $selectedQuery ?? '' }}"
                        placeholder="No laporan, judul, atau lokasi"
                        class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</span>
                    <select name="status"
                        class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                        <option value="">Semua Status</option>
                        <option value="submitted" @selected(($selectedStatus ?? '') === 'submitted')>Submitted</option>
                        <option value="reviewed" @selected(($selectedStatus ?? '') === 'reviewed')>Reviewed</option>
                        <option value="resolved" @selected(($selectedStatus ?? '') === 'resolved')>Resolved</option>
                    </select>
                </label>
            </form>

            <section class="grid gap-4 md:grid-cols-3">
                @foreach (['submitted' => 'Submitted', 'reviewed' => 'Reviewed', 'resolved' => 'Resolved'] as $status => $label)
                    <article class="rounded-[1.25rem] bg-white px-6 py-6 shadow-[0_16px_35px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $label }}</p>
                        <p class="mt-3 text-4xl font-bold text-[var(--primary-color)]">{{ $summaryCounts[$status] }}</p>
                    </article>
                @endforeach
            </section>

            <div data-live-region="user-hazards-table" class="overflow-hidden rounded-[1.45rem] bg-white shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-[var(--primary-color)] text-left text-white">
                            <tr>
                                <th class="px-6 py-4 font-semibold">No. Laporan</th>
                                <th class="px-6 py-4 font-semibold">Judul</th>
                                <th class="px-6 py-4 font-semibold">Lokasi</th>
                                <th class="px-6 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($reports as $report)
                                <tr>
                                    <td class="px-6 py-4 font-bold text-[var(--primary-color)]">{{ $report->report_number }}</td>
                                    <td class="px-6 py-4 text-slate-700">{{ $report->title }}</td>
                                    <td class="px-6 py-4 text-slate-700">{{ $report->location?->name ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $statusBadge($report->status) }}">
                                            {{ str_replace('_', ' ', $report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('user.hazards.show', $report) }}" class="font-semibold text-[var(--primary-color)] hover:opacity-80">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada hazard report yang dikirim.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div data-live-pagination data-live-target="[data-live-region='user-hazards-table']" class="border-t border-slate-200 px-6 py-4">
                    @if (filled($selectedQuery ?? null) || filled($selectedStatus ?? null))
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <p class="text-sm text-slate-500">Riwayat hazard sedang difilter secara realtime.</p>
                            <a href="{{ route('user.hazards.index') }}" class="text-sm font-semibold text-[var(--primary-color)]">
                                Reset Filter
                            </a>
                        </div>
                    @endif
                    {{ $reports->links() }}
                </div>
            </div>
        </section>
    </main>
@endsection
