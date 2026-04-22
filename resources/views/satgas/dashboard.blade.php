@extends('layouts.app')

@section('title', 'Dashboard Satgas')

@section('content')
    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Menunggu Verifikasi</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['submitted_incidents'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Laporan yang perlu ditinjau sekarang.</p>
            </div>
            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Sudah Diverifikasi</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['verified_incidents'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Laporan yang lolos pengecekan awal.</p>
            </div>
            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Dalam Investigasi</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['investigating_incidents'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Kasus yang perlu tindak lanjut lapangan.</p>
            </div>
            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Prioritas Kritis</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['critical_incidents'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Butuh perhatian dan koordinasi cepat.</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Dashboard Satgas</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Ruang kerja verifikasi dan tindak lanjut insiden.</h2>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600">
                Dari sini Satgas dapat meninjau laporan, memverifikasi temuan, dan melanjutkan investigasi atau tindakan korektif.
            </p>

            <div class="mt-8 space-y-3">
                @forelse ($priorityReports as $report)
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $report->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $report->report_number }} • {{ $report->reporter?->name ?? '-' }} • {{ $report->location?->name ?? '-' }}
                                </p>
                            </div>
                            <a href="{{ route('satgas.incidents.show', $report) }}" class="text-sm font-semibold text-cyan-700 hover:text-cyan-800">
                                Tinjau laporan
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 px-5 py-6 text-sm text-slate-500">
                        Belum ada laporan prioritas untuk ditinjau.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-[2rem] bg-slate-900 p-8 text-white shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-300">Fokus Harian</p>
            <div class="mt-5 space-y-4 text-sm leading-7 text-slate-200">
                <p>1. Tinjau {{ $stats['submitted_incidents'] }} laporan baru yang berstatus `submitted`.</p>
                <p>2. Verifikasi kelengkapan kronologi dan lampiran.</p>
                <p>3. Koordinasikan tindak lanjut untuk {{ $stats['critical_incidents'] }} kejadian prioritas tinggi.</p>
            </div>
        </div>
        </div>
    </section>
@endsection
