@extends('layouts.app')

@section('title', 'Review Laporan Insiden')

@section('content')
    <section class="space-y-6">
        <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Satgas Review</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Daftar Laporan Insiden</h2>
            <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                Area ini dipakai Satgas untuk memeriksa laporan masuk, meninjau detail kejadian, dan melakukan verifikasi awal.
            </p>
        </div>

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
                            <th class="px-6 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reports as $report)
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $report->report_number }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $report->reporter?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $report->title }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $report->location?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ str_replace('_', ' ', $report->status) }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('satgas.incidents.show', $report) }}" class="font-semibold text-cyan-700 hover:text-cyan-800">
                                        Tinjau
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-500">Belum ada laporan insiden.</td>
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
