@extends('satgas.layouts.app')

@section('title', 'Profil Satgas')
@section('hero_eyebrow', 'Profil Satgas')
@section('hero_title', 'Identitas dan kontak operasional')
@section('hero_description', 'Kelola data dasar akun Satgas agar notifikasi, koordinasi, dan tindak lanjut laporan tetap akurat.')

@section('content')
    @php
        $initials = collect(preg_split('/\s+/', trim($user->name)) ?: [])
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');
    @endphp

    <section class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <article class="section-shell overflow-hidden rounded-[2rem] p-6 shadow-[0_24px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/90 lg:p-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                <div class="relative mx-auto sm:mx-0">
                    <div class="flex h-28 w-28 items-center justify-center rounded-[2rem] bg-gradient-to-br from-[var(--primary-color)] via-[#2d7be5] to-[#8dbbff] text-4xl font-bold text-white shadow-[0_20px_40px_rgba(10,77,179,0.28)]">
                        {{ $initials !== '' ? $initials : 'S' }}
                    </div>
                    <span class="absolute -bottom-2 -right-2 inline-flex h-9 w-9 items-center justify-center rounded-full border-4 border-white bg-[var(--green)]">
                        <span class="material-symbols-outlined text-base text-white">verified</span>
                    </span>
                </div>

                <div class="flex-1 text-center sm:text-left">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]/60">Akun Satgas Aktif</p>
                    <h3 class="mt-2 text-3xl font-bold text-slate-900">{{ $user->name }}</h3>
                    <p class="mt-2 text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $user->username ?: 'Username belum diatur' }}</p>
                    <p class="mt-3 text-base font-semibold leading-7 text-slate-600">
                        {{ $roleLabel }}
                    </p>
                </div>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                @foreach ($stats as $stat)
                    <div class="rounded-[1.4rem] bg-white px-4 py-5 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl {{ $stat['tone'] }}">
                            <span class="material-symbols-outlined">{{ $stat['icon'] }}</span>
                        </span>
                        <p class="mt-4 text-3xl font-bold text-slate-900">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-500">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2">
                <div class="rounded-[1.5rem] bg-white px-5 py-5 ring-1 ring-slate-100">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Email</p>
                    <p class="mt-2 break-all text-lg font-bold text-slate-900">{{ $user->email }}</p>
                </div>
                <div class="rounded-[1.5rem] bg-white px-5 py-5 ring-1 ring-slate-100">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Nomor WhatsApp</p>
                    <p class="mt-2 text-lg font-bold text-slate-900">{{ $user->phone ?: 'Belum diatur' }}</p>
                </div>
            </div>
        </article>

        <article class="section-shell rounded-[2rem] p-6 shadow-[0_24px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/90 lg:p-8">
            <div class="border-b border-slate-200 pb-5">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Edit Profil</p>
                <h3 class="mt-3 text-3xl font-bold text-slate-900">Perbarui data kontak Satgas</h3>
                <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600">
                    Nomor WhatsApp di akun ini dipakai sebagai target notifikasi saat laporan insiden atau hazard baru masuk.
                </p>
            </div>

            <form action="{{ route('satgas.profile.update') }}" method="POST" class="mt-8 space-y-6">
                @csrf
                @method('PATCH')

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-3 md:col-span-2">
                        <label for="name" class="block text-sm font-bold uppercase tracking-[0.2em] text-[var(--primary-color)]">Nama Lengkap</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                            class="w-full rounded-[1rem] border border-slate-200 bg-white px-5 py-4 text-base font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                        @error('name')
                            <p class="text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3">
                        <label for="username" class="block text-sm font-bold uppercase tracking-[0.2em] text-[var(--primary-color)]">Username</label>
                        <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}"
                            class="w-full rounded-[1rem] border border-slate-200 bg-white px-5 py-4 text-base font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                        @error('username')
                            <p class="text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3">
                        <label for="phone" class="block text-sm font-bold uppercase tracking-[0.2em] text-[var(--primary-color)]">Nomor WhatsApp</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx"
                            class="w-full rounded-[1rem] border border-slate-200 bg-white px-5 py-4 text-base font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                        @error('phone')
                            <p class="text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="rounded-[1.4rem] bg-[#f8fbff] px-5 py-4 ring-1 ring-[var(--primary-color)]/8">
                    <p class="text-sm leading-7 text-slate-700">
                        Pastikan nomor menggunakan nomor WhatsApp aktif. Sistem akan memakai nomor ini untuk notifikasi laporan baru dari portal publik.
                    </p>
                </div>

                <button type="submit"
                    class="inline-flex min-h-14 items-center justify-center rounded-full bg-[var(--primary-color)] px-7 text-base font-bold text-white shadow-[0_15px_30px_rgba(10,77,179,0.18)] transition hover:-translate-y-1">
                    Simpan Profil
                </button>
            </form>
        </article>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:p-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Penugasan Insiden</p>
                    <h3 class="mt-3 text-2xl font-bold text-slate-900">Insiden terbaru yang ditugaskan</h3>
                </div>
                <a href="{{ route('satgas.incidents.index') }}" class="text-sm font-bold text-[var(--primary-color)]">Lihat semua</a>
            </div>

            <div class="mt-6 space-y-3">
                @forelse ($recentAssignments as $report)
                    <a href="{{ route('satgas.incidents.show', $report) }}" class="block rounded-[1.2rem] bg-[#f8fbff] px-4 py-4 ring-1 ring-slate-200 transition hover:bg-white">
                        <p class="text-sm font-bold text-slate-900">{{ $report->title }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ $report->report_number }} / {{ $report->status }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $report->location?->name ?? '-' }}</p>
                    </a>
                @empty
                    <div class="rounded-[1.2rem] border border-dashed border-slate-300 bg-[#f8fbff] px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada insiden yang ditugaskan langsung ke akun ini.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:p-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Review Hazard</p>
                    <h3 class="mt-3 text-2xl font-bold text-slate-900">Hazard yang pernah ditangani</h3>
                </div>
                <a href="{{ route('satgas.hazards.index') }}" class="text-sm font-bold text-[var(--primary-color)]">Lihat semua</a>
            </div>

            <div class="mt-6 space-y-3">
                @forelse ($recentHazards as $hazard)
                    <a href="{{ route('satgas.hazards.show', $hazard) }}" class="block rounded-[1.2rem] bg-[#f8fbff] px-4 py-4 ring-1 ring-slate-200 transition hover:bg-white">
                        <p class="text-sm font-bold text-slate-900">{{ $hazard->title }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ $hazard->report_number }} / {{ $hazard->status }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $hazard->location?->name ?? '-' }}</p>
                    </a>
                @empty
                    <div class="rounded-[1.2rem] border border-dashed border-slate-300 bg-[#f8fbff] px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada hazard yang direview atau diselesaikan oleh akun ini.
                    </div>
                @endforelse
            </div>
        </article>
    </section>
@endsection
