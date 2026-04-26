@extends('user.layouts.app')

@section('title', 'Edit Profil')

@section('page')
    <header id="header" class="relative flex min-h-[32rem] w-full items-end overflow-hidden px-4 pb-12 pt-32 lg:px-8">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-24 top-18 h-56 w-56 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute right-0 top-20 h-72 w-72 rounded-full bg-[var(--orange)]/18 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/2 h-32 w-[82%] -translate-x-1/2 rounded-full bg-white/12 blur-3xl"></div>
        </div>

        <div class="relative mx-auto grid w-full max-w-[1600px] gap-8 xl:grid-cols-[1.05fr_0.95fr]">
            <section class="space-y-6 text-white">
                <span class="inline-flex w-fit rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white/90">
                    Portal Operasional K3L
                </span>
                <div class="space-y-4">
                    <h1 class="max-w-4xl text-4xl font-bold leading-tight text-white lg:text-6xl">
                        Perbarui profil Anda dengan tampilan yang lebih rapi dan fokus.
                    </h1>
                    <p class="max-w-3xl text-base leading-8 text-white/88 lg:text-xl">
                        Ubah data dasar yang sering dipakai pada pelaporan dan aktivitas sistem agar identitas Anda tetap akurat dan mudah dikenali.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('user.profile.show') }}"
                        class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-bold text-[var(--primary-color)] shadow-[0_14px_30px_rgba(255,255,255,0.18)] transition hover:-translate-y-1">
                        Kembali ke Profil
                    </a>
                </div>
            </section>

            <section class="relative">
                <div class="frosted-panel overflow-hidden rounded-[2rem] p-6 shadow-[0_28px_80px_rgba(7,45,112,0.22)]">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                        <div class="relative mx-auto sm:mx-0">
                            <div class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-[2rem] bg-gradient-to-br from-[var(--primary-color)] via-[#2d7be5] to-[#8dbbff] text-4xl font-bold text-white shadow-[0_20px_40px_rgba(10,77,179,0.28)]">
                                {{ strtoupper(substr($profileCard['name'], 0, 1)) }}
                            </div>
                            <span class="absolute -bottom-2 -right-2 inline-flex h-9 w-9 items-center justify-center rounded-full border-4 border-white bg-[var(--primary-color)]">
                                <span class="material-symbols-outlined text-base text-white">edit</span>
                            </span>
                        </div>

                        <div class="flex-1 text-center sm:text-left">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]/60">Profil yang Diedit</p>
                            <h2 class="mt-2 text-3xl font-bold text-slate-900">{{ $profileCard['name'] }}</h2>
                            <p class="mt-2 text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $profileCard['identifier'] }}</p>
                            <p class="mt-3 text-base font-semibold leading-7 text-slate-600">{{ $profileCard['roleLabel'] }}</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-[1.4rem] bg-[var(--blue-low-opacity)]/65 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Email Referensi</p>
                            <p class="mt-2 break-all text-lg font-bold text-[var(--primary-color)]">{{ $user->email }}</p>
                        </div>
                        <div class="rounded-[1.4rem] bg-[#eef8f1] px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Info Medis</p>
                            <p class="mt-2 text-lg font-bold text-[var(--green)]">{{ $profileCard['medicalInfo'] }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </header>

    <main class="w-full bg-[#f6f8fc] pt-25 pb-20">
        <section class="w-full px-4 lg:px-8">
            <div class="mx-auto -mt-10 flex w-full max-w-[1600px] flex-col gap-6 lg:-mt-14">
                @include('partials.flash')

                <section class="grid gap-6 xl:grid-cols-[0.82fr_1.18fr]">
                    <article class="section-shell rounded-[2rem] p-6 shadow-[0_24px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/90 lg:p-8">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Panduan Singkat</p>
                            <h3 class="mt-3 text-3xl font-bold text-slate-900">Pastikan data utama Anda selalu siap dipakai.</h3>
                            <p class="mt-4 text-base leading-8 text-slate-600">
                                Informasi di bawah ini membantu menjaga identitas Anda konsisten saat membuat laporan, menerima pembaruan, dan berinteraksi dengan sistem.
                            </p>
                        </div>

                        <div class="mt-8 grid gap-4">
                            <div class="rounded-[1.6rem] bg-white px-5 py-5 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Nama Lengkap</p>
                                <p class="mt-2 text-lg font-bold text-slate-900">Gunakan nama yang mudah dikenali untuk proses verifikasi.</p>
                            </div>

                            <div class="rounded-[1.6rem] bg-white px-5 py-5 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Nomor Telepon</p>
                                <p class="mt-2 text-lg font-bold text-slate-900">Nomor aktif membantu koordinasi saat ada tindak lanjut laporan.</p>
                            </div>

                            @if ($studentProfile)
                                <div class="rounded-[1.6rem] bg-white px-5 py-5 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Kelas / Angkatan</p>
                                    <p class="mt-2 text-lg font-bold text-slate-900">Lengkapi data kelas agar konteks akademik Anda tetap akurat.</p>
                                </div>
                            @endif

                            <div class="rounded-[1.6rem] bg-[var(--primary-deep)] px-5 py-5 text-white shadow-[0_18px_40px_rgba(7,45,112,0.18)]">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/60">Catatan Sistem</p>
                                <p class="mt-3 text-sm leading-7 text-white/85">
                                    Email, NIM/NIP, dan data akademik inti digunakan sebagai referensi sistem. Jika perlu perubahan data inti atau data medis, hubungi admin sistem.
                                </p>
                            </div>
                        </div>
                    </article>

                    <article class="section-shell overflow-hidden rounded-[2rem] p-6 shadow-[0_24px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/90 lg:p-8">
                        <div class="border-b border-slate-200 pb-5">
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Edit Profil</p>
                            <h3 class="mt-3 text-3xl font-bold text-slate-900">Perbarui identitas dasar Anda</h3>
                            <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600">
                                Isi data berikut dengan informasi terbaru agar halaman profil dan proses pelaporan tetap konsisten.
                            </p>
                        </div>

                        <form action="{{ route('user.profile.update') }}" method="POST" class="mt-8 space-y-6">
                            @csrf
                            @method('PATCH')

                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="space-y-3">
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
                                    <label for="phone" class="block text-sm font-bold uppercase tracking-[0.2em] text-[var(--primary-color)]">Nomor Telepon</label>
                                    <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                                        class="w-full rounded-[1rem] border border-slate-200 bg-white px-5 py-4 text-base font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                                    @error('phone')
                                        <p class="text-sm font-medium text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if ($studentProfile)
                                    <div class="space-y-3">
                                        <label for="class_name" class="block text-sm font-bold uppercase tracking-[0.2em] text-[var(--primary-color)]">Kelas / Angkatan</label>
                                        <input id="class_name" name="class_name" type="text" value="{{ old('class_name', $studentProfile?->class_name) }}"
                                            class="w-full rounded-[1rem] border border-slate-200 bg-white px-5 py-4 text-base font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                                        @error('class_name')
                                            <p class="text-sm font-medium text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>

                            <div class="rounded-[1.4rem] bg-[#f8fbff] px-5 py-4 ring-1 ring-[var(--primary-color)]/8">
                                <p class="text-sm leading-7 text-slate-700">
                                    Data referensi seperti email dan identitas utama kampus tetap dipertahankan oleh sistem. Untuk perubahan data inti lain, silakan koordinasi dengan admin.
                                </p>
                            </div>

                            <div class="flex flex-col gap-3 pt-2 sm:flex-row">
                                <button type="submit"
                                    class="inline-flex min-h-14 items-center justify-center rounded-full bg-[var(--primary-color)] px-7 text-base font-bold text-white shadow-[0_15px_30px_rgba(10,77,179,0.18)] transition hover:-translate-y-1">
                                    Simpan Perubahan
                                </button>
                                <a href="{{ route('user.profile.show') }}"
                                    class="inline-flex min-h-14 items-center justify-center rounded-full border border-[var(--primary-color)]/20 bg-white px-7 text-base font-bold text-[var(--primary-color)] transition hover:bg-[var(--blue-low-opacity)]/35">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </article>
                </section>
            </div>
        </section>
    </main>
@endsection
