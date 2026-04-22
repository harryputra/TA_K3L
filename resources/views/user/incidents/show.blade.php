@extends('layouts.app')

@section('title', 'Detail Laporan Insiden')

@section('content')
    <section class="space-y-6">
        <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Detail Laporan</p>
                    <h2 class="mt-2 text-3xl font-semibold text-slate-900">{{ $incidentReport->title }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Nomor laporan: <span class="font-semibold text-slate-900">{{ $incidentReport->report_number }}</span>
                    </p>
                </div>

                <span class="inline-flex rounded-full bg-amber-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-amber-800">
                    {{ str_replace('_', ' ', $incidentReport->status) }}
                </span>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="space-y-6">
                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Informasi Kejadian</h3>
                    <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Kategori</dt>
                            <dd class="mt-2 text-sm text-slate-800">{{ $incidentReport->category?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Lokasi</dt>
                            <dd class="mt-2 text-sm text-slate-800">{{ $incidentReport->location?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tanggal</dt>
                            <dd class="mt-2 text-sm text-slate-800">{{ optional($incidentReport->incident_date)->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Keparahan</dt>
                            <dd class="mt-2 text-sm text-slate-800">{{ ucfirst($incidentReport->severity_level) }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Kronologi dan Dampak</h3>
                    <div class="mt-6 space-y-5 text-sm leading-7 text-slate-700">
                        <div>
                            <p class="font-semibold text-slate-900">Kronologi</p>
                            <p class="mt-2">{{ $incidentReport->chronology }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Penyebab Awal</p>
                            <p class="mt-2">{{ $incidentReport->cause ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Tindakan Awal</p>
                            <p class="mt-2">{{ $incidentReport->initial_action ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Dampak</p>
                            <p class="mt-2">{{ $incidentReport->impact ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Riwayat Status</h3>
                    <div class="mt-6 space-y-4">
                        @forelse ($incidentReport->statusHistories->sortByDesc('created_at') as $history)
                            <div class="rounded-2xl bg-slate-50 px-4 py-4">
                                <p class="text-sm font-semibold text-slate-900">
                                    {{ str_replace('_', ' ', $history->to_status) }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ optional($history->created_at)->format('d M Y H:i') }} oleh {{ $history->changer?->name ?? 'Sistem' }}
                                </p>
                                @if ($history->change_note)
                                    <p class="mt-2 text-sm text-slate-700">{{ $history->change_note }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada riwayat status.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Lampiran</h3>
                    <div class="mt-6 space-y-3">
                        @forelse ($incidentReport->attachments as $attachment)
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                {{ $attachment->file_name }}
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Tidak ada lampiran pada laporan ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
