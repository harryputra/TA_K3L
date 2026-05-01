@extends('admin.layouts.app')

@section('title', 'Monitoring Hazard Report')

@section('content')
    @php
        $statusBadge = fn (string $status): string => match ($status) {
            'submitted' => 'bg-amber-100 text-amber-800',
            'reviewed' => 'bg-sky-100 text-sky-800',
            'resolved' => 'bg-emerald-100 text-emerald-800',
            default => 'bg-slate-100 text-slate-600',
        };
    @endphp

    <section class="space-y-6">
        <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Admin Hazard Monitoring</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Pantau Hazard Report</h2>
            <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                Admin dapat memonitor seluruh hazard report, status penanganan, dan siapa yang melakukan review maupun penyelesaian.
            </p>
        </div>

        <section class="grid gap-4 md:grid-cols-3">
            @foreach (['submitted' => 'Submitted', 'reviewed' => 'Reviewed', 'resolved' => 'Resolved'] as $status => $label)
                <article class="rounded-[1.5rem] bg-white px-5 py-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $label }}</p>
                    <p class="mt-3 text-4xl font-bold text-slate-900">{{ $summaryCounts[$status] }}</p>
                </article>
            @endforeach
        </section>

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">No. Laporan</th>
                            <th class="px-6 py-4 font-semibold">Pelapor</th>
                            <th class="px-6 py-4 font-semibold">Judul</th>
                            <th class="px-6 py-4 font-semibold">Lokasi</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Satgas</th>
                            <th class="px-6 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reports as $report)
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $report->report_number }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $report->reporter?->name ?? $report->reporter_name ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $report->title }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $report->location?->name ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $statusBadge($report->status) }}">
                                        {{ str_replace('_', ' ', $report->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-700">{{ $report->resolver?->name ?? $report->reviewer?->name ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.hazards.show', $report) }}" class="font-semibold text-[var(--primary-color)] hover:text-[var(--primary-deep)]">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-500">Belum ada hazard report.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $reports->links() }}
            </div>
        </div>
    </section>
@endsection
