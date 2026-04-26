@extends('admin.layouts.app')

@section('title', 'Detail Hazard Report')

@section('content')
    @php
        $statusBadge = match ($hazardReport->status) {
            'submitted' => 'bg-amber-100 text-amber-800',
            'reviewed' => 'bg-sky-100 text-sky-800',
            'resolved' => 'bg-emerald-100 text-emerald-800',
            default => 'bg-slate-100 text-slate-600',
        };
    @endphp

    <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-6">
            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Admin Hazard Monitoring</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">{{ $hazardReport->title }}</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">
                    Laporan dari <span class="font-semibold text-slate-900">{{ $hazardReport->reporter?->name ?? '-' }}</span>
                    dengan nomor <span class="font-semibold text-slate-900">{{ $hazardReport->report_number }}</span>.
                </p>
            </div>

            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Ringkasan Hazard</h3>
                <div class="mt-6 grid gap-5 text-sm sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Jenis Temuan</p>
                        <p class="mt-2 text-slate-800">{{ str_replace('-', ' ', $hazardReport->hazard_type) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Lokasi</p>
                        <p class="mt-2 text-slate-800">{{ $hazardReport->location?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Pelapor</p>
                        <p class="mt-2 text-slate-800">{{ $hazardReport->reporter?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</p>
                        <p class="mt-2">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $statusBadge }}">
                                {{ str_replace('_', ' ', $hazardReport->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-8 space-y-5 text-sm leading-7 text-slate-700">
                    <div>
                        <p class="font-semibold text-slate-900">Catatan Pelapor</p>
                        <p class="mt-2">{{ $hazardReport->notes ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Respons Satgas</p>
                        <p class="mt-2">{{ $hazardReport->response_note ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Metadata Penanganan</h3>
                <div class="mt-6 space-y-4 text-sm">
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="font-semibold text-slate-900">Dikirim</p>
                        <p class="mt-1 text-slate-600">{{ optional($hazardReport->submitted_at)->format('d M Y H:i') ?? '-' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="font-semibold text-slate-900">Ditinjau</p>
                        <p class="mt-1 text-slate-600">{{ optional($hazardReport->reviewed_at)->format('d M Y H:i') ?? '-' }}</p>
                        <p class="mt-1 text-xs text-slate-500">oleh {{ $hazardReport->reviewer?->name ?? '-' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="font-semibold text-slate-900">Diselesaikan</p>
                        <p class="mt-1 text-slate-600">{{ optional($hazardReport->resolved_at)->format('d M Y H:i') ?? '-' }}</p>
                        <p class="mt-1 text-xs text-slate-500">oleh {{ $hazardReport->resolver?->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Lampiran</h3>
                <div class="mt-6 space-y-3">
                    @forelse ($hazardReport->attachments as $attachment)
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-700">
                            <span class="font-medium">{{ $attachment->file_name }}</span>
                            <span class="material-symbols-outlined text-[var(--primary-color)]">attach_file</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Tidak ada lampiran pada hazard report ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
