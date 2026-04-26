@extends('admin.layouts.app')

@section('title', 'Master Lokasi')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col justify-between gap-4 rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Admin Master Data</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Kelola Lokasi</h2>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                    Lokasi dipakai oleh modul pelaporan insiden, daily safety check, dan aktivitas K3L lainnya.
                </p>
            </div>

            <a href="{{ route('admin.locations.create') }}" class="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                Tambah Lokasi
            </a>
        </div>

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Nama</th>
                            <th class="px-6 py-4 font-semibold">Kode</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Deskripsi</th>
                            <th class="px-6 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($locations as $location)
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $location->name }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $location->code ?: '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $location->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $location->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-700">{{ $location->description ?: '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.locations.edit', $location) }}" class="font-semibold text-[var(--primary-color)] hover:text-[var(--primary-deep)]">Edit</a>
                                        <form action="{{ route('admin.locations.destroy', $location) }}" method="POST"
                                            data-confirm-action="lokasi"
                                            data-confirm-item="{{ $location->name }}"
                                            data-confirm-severity="critical"
                                            data-confirm-message="Lokasi ini bisa dipakai oleh laporan insiden, hazard, dan data operasional lain. Hapus hanya jika benar-benar sudah tidak digunakan.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold text-rose-700 hover:text-rose-800">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada data lokasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $locations->links() }}
            </div>
        </div>
    </section>
@endsection
