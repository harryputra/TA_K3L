@extends('admin.layouts.app')

@section('title', 'Kelola Akun')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col justify-between gap-4 rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Admin Accounts</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Kelola Semua Akun</h2>
                <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600">
                    Admin dapat mengelola seluruh akun mahasiswa, satgas, dan admin dari satu halaman, termasuk role, status aktif, dan informasi login dasarnya.
                </p>
            </div>

            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                Tambah Akun
            </a>
        </div>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <article class="ambient-card rounded-[1.5rem] px-5 py-5 shadow-[0_14px_30px_rgba(15,23,42,0.07)] ring-1 ring-white/80">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Total Akun</p>
                <p class="mt-3 text-4xl font-bold text-[var(--primary-color)]">{{ $summary['total'] }}</p>
            </article>
            <article class="ambient-card rounded-[1.5rem] px-5 py-5 shadow-[0_14px_30px_rgba(15,23,42,0.07)] ring-1 ring-white/80">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Admin</p>
                <p class="mt-3 text-4xl font-bold text-[var(--primary-color)]">{{ $summary['admin'] }}</p>
            </article>
            <article class="ambient-card rounded-[1.5rem] px-5 py-5 shadow-[0_14px_30px_rgba(15,23,42,0.07)] ring-1 ring-white/80">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Satgas</p>
                <p class="mt-3 text-4xl font-bold text-[var(--primary-color)]">{{ $summary['satgas'] }}</p>
            </article>
            <article class="ambient-card rounded-[1.5rem] px-5 py-5 shadow-[0_14px_30px_rgba(15,23,42,0.07)] ring-1 ring-white/80">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Mahasiswa</p>
                <p class="mt-3 text-4xl font-bold text-[var(--primary-color)]">{{ $summary['mahasiswa'] }}</p>
            </article>
            <article class="ambient-card rounded-[1.5rem] px-5 py-5 shadow-[0_14px_30px_rgba(15,23,42,0.07)] ring-1 ring-white/80">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Akun Aktif</p>
                <p class="mt-3 text-4xl font-bold text-[var(--green)]">{{ $summary['active'] }}</p>
            </article>
        </section>

        <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <form method="GET" action="{{ route('admin.users.index') }}" data-auto-submit-form data-live-submit
                data-live-target="[data-live-region='admin-users-table']" data-auto-submit-delay="300"
                class="grid gap-4 lg:grid-cols-[1.2fr_0.8fr_0.8fr_auto]">
                <input type="text" name="q" value="{{ $selectedQuery }}" placeholder="Cari nama, email, atau username"
                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">

                <select name="role"
                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                    <option value="">Semua role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->code }}" @selected($selectedRole === $role->code)>{{ $role->name }}</option>
                    @endforeach
                </select>

                <select name="status"
                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                    <option value="">Semua status</option>
                    <option value="active" @selected($selectedStatus === 'active')>Aktif</option>
                    <option value="inactive" @selected($selectedStatus === 'inactive')>Nonaktif</option>
                </select>

                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                    Filter
                </button>
            </form>
        </div>

        <div data-live-region="admin-users-table" class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Nama</th>
                            <th class="px-6 py-4 font-semibold">Email</th>
                            <th class="px-6 py-4 font-semibold">Username</th>
                            <th class="px-6 py-4 font-semibold">Role</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Telepon</th>
                            <th class="px-6 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $account)
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $account->name }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $account->email }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $account->username ?: '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full bg-[var(--blue-low-opacity)] px-3 py-1 text-xs font-semibold text-[var(--primary-color)]">
                                        {{ $account->role?->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $account->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $account->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-700">{{ $account->phone ?: '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.users.edit', $account) }}" class="font-semibold text-[var(--primary-color)] hover:text-[var(--primary-deep)]">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-500">Belum ada akun yang bisa ditampilkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div data-live-pagination data-live-target="[data-live-region='admin-users-table']" class="border-t border-slate-200 px-6 py-4">
                {{ $users->links() }}
            </div>
        </div>
    </section>
@endsection
