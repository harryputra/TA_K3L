@extends('layouts.app')

@section('title', 'Laporan Insiden Saya')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col justify-between gap-4 rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Riwayat Pelaporan</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Laporan Insiden Saya</h2>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                    Halaman ini menampilkan laporan yang dibuat oleh pengguna yang sedang login beserta status prosesnya.
                </p>
            </div>

            <a href="{{ route('user.incidents.create') }}" class="inline-flex items-center justify-center rounded-full bg-cyan-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-cyan-800">
                Buat Laporan Baru
            </a>
        </div>

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">No. Laporan</th>
                            <th class="px-6 py-4 font-semibold">Judul</th>
                            <th class="px-6 py-4 font-semibold">Kategori</th>
                            <th class="px-6 py-4 font-semibold">Lokasi</th>
                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reports as $report)
                            <tr class="align-top">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $report->report_number }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $report->title }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $report->category?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $report->location?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ optional($report->incident_date)->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-800">
                                        {{ str_replace('_', ' ', $report->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                                    Belum ada laporan insiden yang dikirim.
                                </td>
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
