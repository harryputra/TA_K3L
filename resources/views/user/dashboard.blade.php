@extends('layouts.app')

@section('title', 'Dashboard Pengguna')

@section('content')
    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Laporan Saya</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['my_reports'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Total laporan yang pernah Anda kirim.</p>
            </div>
            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Menunggu Review</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['submitted_reports'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Masih menunggu verifikasi Satgas.</p>
            </div>
            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Sudah Diverifikasi</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['verified_reports'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Laporan yang sudah lolos pemeriksaan awal.</p>
            </div>
            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Konten K3L</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $publishedKnowledgeCount }}</p>
                <p class="mt-2 text-sm text-slate-600">Artikel keselamatan yang sudah dipublikasikan.</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Ringkasan</p>
            <h2 class="mt-3 text-3xl font-semibold text-slate-900">Area pengguna K3L siap dipakai sebagai fondasi frontend.</h2>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600">
                Pada fase ini, fokus implementasi diarahkan ke pelaporan insiden. Dashboard ini sengaja dibuat ringan agar nanti
                bisa diisi metrik status laporan, panduan keselamatan terbaru, dan notifikasi tindak lanjut.
            </p>

            <div class="mt-8 space-y-3">
                @forelse ($recentReports as $report)
                    <a href="{{ route('user.incidents.show', $report) }}" class="block rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4 transition hover:border-cyan-200 hover:bg-cyan-50">
                        <p class="text-sm font-semibold text-slate-900">{{ $report->title }}</p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $report->report_number }} • {{ $report->category?->name ?? '-' }} • {{ $report->location?->name ?? '-' }}
                        </p>
                    </a>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 px-5 py-6 text-sm text-slate-500">
                        Belum ada laporan insiden yang Anda kirim.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-[2rem] bg-slate-900 p-8 text-white shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-300">Langkah Cepat</p>
            <div class="mt-5 space-y-4 text-sm leading-7 text-slate-200">
                <p>1. Buat laporan insiden baru melalui menu <strong>Laporan Insiden</strong>.</p>
                <p>2. Saat ini ada {{ $stats['submitted_reports'] }} laporan Anda yang masih menunggu review.</p>
                <p>3. Lampirkan bukti foto atau dokumen jika tersedia.</p>
            </div>
        </div>
        </div>
    </section>
@endsection
