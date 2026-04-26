@extends('admin.layouts.app')

@section('title', 'Langkah Tanggap Cepat')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col justify-between gap-4 rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Admin Emergency</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Kelola Langkah Tanggap Cepat</h2>
            </div>
            <a href="{{ route('admin.emergency-response-steps.create') }}" class="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                Tambah Langkah
            </a>
        </div>

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Judul</th>
                            <th class="px-6 py-4 font-semibold">Aktif</th>
                            <th class="px-6 py-4 font-semibold">Urutan</th>
                            <th class="px-6 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($steps as $step)
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $step->title }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $step->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $step->sort_order }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.emergency-response-steps.edit', $step) }}" class="font-semibold text-[var(--primary-color)] hover:text-[var(--primary-deep)]">Edit</a>
                                        <form action="{{ route('admin.emergency-response-steps.destroy', $step) }}" method="POST"
                                            data-confirm-action="langkah tanggap cepat"
                                            data-confirm-item="{{ $step->title }}"
                                            data-confirm-message="Langkah tanggap cepat yang dihapus dapat memengaruhi panduan darurat yang dibaca pengguna. Pastikan data ini memang tidak diperlukan.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold text-rose-700 hover:text-rose-800">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-500">Belum ada langkah tanggap cepat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-6 py-4">{{ $steps->links() }}</div>
        </div>
    </section>
@endsection
