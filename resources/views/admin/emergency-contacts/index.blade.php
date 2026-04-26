@extends('admin.layouts.app')

@section('title', 'Kontak Darurat')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col justify-between gap-4 rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Admin Emergency</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Kelola Kontak Darurat</h2>
            </div>
            <a href="{{ route('admin.emergency-contacts.create') }}" class="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                Tambah Kontak
            </a>
        </div>

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Nama</th>
                            <th class="px-6 py-4 font-semibold">Telepon</th>
                            <th class="px-6 py-4 font-semibold">Aktif</th>
                            <th class="px-6 py-4 font-semibold">Urutan</th>
                            <th class="px-6 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($contacts as $contact)
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $contact->name }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $contact->phone_number }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $contact->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $contact->sort_order }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.emergency-contacts.edit', $contact) }}" class="font-semibold text-[var(--primary-color)] hover:text-[var(--primary-deep)]">Edit</a>
                                        <form action="{{ route('admin.emergency-contacts.destroy', $contact) }}" method="POST"
                                            data-confirm-action="kontak darurat"
                                            data-confirm-item="{{ $contact->name }} - {{ $contact->phone_number }}"
                                            data-confirm-severity="critical"
                                            data-confirm-message="Kontak darurat yang dihapus tidak lagi muncul untuk pengguna. Pastikan nomor penting tidak terhapus tanpa sengaja.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold text-rose-700 hover:text-rose-800">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada kontak darurat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-6 py-4">{{ $contacts->links() }}</div>
        </div>
    </section>
@endsection
