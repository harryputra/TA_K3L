@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Total Pengguna</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['total_users'] }}</p>
                <p class="mt-2 text-sm text-slate-600">{{ $stats['active_users'] }} akun aktif di sistem.</p>
            </div>
            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Laporan Insiden</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['incident_count'] }}</p>
                <p class="mt-2 text-sm text-slate-600">{{ $stats['submitted_incidents'] }} menunggu review awal.</p>
            </div>
            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Satgas Aktif</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['satgas_count'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Personel verifikasi dan tindak lanjut.</p>
            </div>
            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Master Data</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['location_count'] }}</p>
                <p class="mt-2 text-sm text-slate-600">{{ $stats['role_count'] }} role sistem tersedia.</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Dashboard Admin</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Pusat kendali sistem K3L.</h2>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600">
                Area ini dipersiapkan untuk pengelolaan akun, master data, dashboard statistik global, dan pelaporan lintas modul.
            </p>

            <div class="mt-8 overflow-hidden rounded-3xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-5 py-4 font-semibold">No. Laporan</th>
                            <th class="px-5 py-4 font-semibold">Pelapor</th>
                            <th class="px-5 py-4 font-semibold">Lokasi</th>
                            <th class="px-5 py-4 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentReports as $report)
                            <tr>
                                <td class="px-5 py-4 font-medium text-slate-900">{{ $report->report_number }}</td>
                                <td class="px-5 py-4 text-slate-700">{{ $report->reporter?->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-700">{{ $report->location?->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-700">{{ str_replace('_', ' ', $report->status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-slate-500">Belum ada laporan terbaru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-[2rem] bg-slate-900 p-8 text-white shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-300">Prioritas</p>
            <div class="mt-5 space-y-4 text-sm leading-7 text-slate-200">
                <p>1. Verifikasi data master role, lokasi, dan kategori insiden.</p>
                <p>2. {{ $stats['verified_incidents'] }} laporan sudah terverifikasi dan siap dilanjutkan.</p>
                <p>3. Kelola akses Admin, Satgas, dan Mahasiswa secara konsisten.</p>
            </div>

            <div class="mt-8 grid gap-3">
                <a href="{{ route('admin.locations.index') }}" class="rounded-2xl bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                    Kelola Lokasi
                </a>
                <a href="{{ route('admin.incident-categories.index') }}" class="rounded-2xl bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                    Kelola Kategori Insiden
                </a>
            </div>
        </div>
        </div>
    </section>
@endsection
