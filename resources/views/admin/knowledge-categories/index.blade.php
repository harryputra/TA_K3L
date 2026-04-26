@extends('admin.layouts.app')

@section('title', 'Knowledge Categories')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col justify-between gap-4 rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Admin Knowledge</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Kelola Kategori Knowledge</h2>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Kategori knowledge dipakai untuk pengelompokan materi di knowledge center dan panel admin.</p>
            </div>

            <a href="{{ route('admin.knowledge-categories.create') }}" class="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                Tambah Kategori
            </a>
        </div>

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Nama</th>
                            <th class="px-6 py-4 font-semibold">Slug</th>
                            <th class="px-6 py-4 font-semibold">Dipakai Materi</th>
                            <th class="px-6 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($categories as $category)
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $category->name }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $category->slug }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $category->articles_count }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.knowledge-categories.edit', $category) }}" class="font-semibold text-[var(--primary-color)] hover:text-[var(--primary-deep)]">Edit</a>
                                        <form action="{{ route('admin.knowledge-categories.destroy', $category) }}" method="POST"
                                            data-confirm-action="kategori knowledge"
                                            data-confirm-item="{{ $category->name }}"
                                            data-confirm-severity="critical"
                                            data-confirm-message="Kategori yang dihapus dapat memengaruhi pengelompokan materi yang sudah ada. Pastikan perubahan ini memang diperlukan.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold text-rose-700 hover:text-rose-800">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-500">Belum ada kategori knowledge.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $categories->links() }}
            </div>
        </div>
    </section>
@endsection
