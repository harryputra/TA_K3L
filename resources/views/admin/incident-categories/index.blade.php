@extends('admin.layouts.app')

@section('title', 'Kategori Insiden')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col justify-between gap-4 rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Admin Master Data</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Kelola Kategori Insiden</h2>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                    Kategori insiden membantu klasifikasi laporan agar proses analisis dan pelaporan lebih rapi.
                </p>
            </div>

            <a href="{{ route('admin.incident-categories.create') }}" class="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                Tambah Kategori
            </a>
        </div>

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Nama</th>
                            <th class="px-6 py-4 font-semibold">Dipakai di Laporan</th>
                            <th class="px-6 py-4 font-semibold">Deskripsi</th>
                            <th class="px-6 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($incidentCategories as $incidentCategory)
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $incidentCategory->name }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $incidentCategory->incident_reports_count }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $incidentCategory->description ?: '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.incident-categories.edit', $incidentCategory) }}" class="font-semibold text-[var(--primary-color)] hover:text-[var(--primary-deep)]">Edit</a>
                                        <form action="{{ route('admin.incident-categories.destroy', $incidentCategory) }}" method="POST"
                                            data-confirm-action="kategori insiden"
                                            data-confirm-item="{{ $incidentCategory->name }}"
                                            data-confirm-severity="critical"
                                            data-confirm-message="Kategori insiden yang dihapus dapat memengaruhi klasifikasi laporan yang sudah ada. Pastikan keputusan ini aman.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold text-rose-700 hover:text-rose-800">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-500">Belum ada kategori insiden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $incidentCategories->links() }}
            </div>
        </div>
    </section>
@endsection
