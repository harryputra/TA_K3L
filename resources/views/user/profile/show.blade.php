@extends('user.layouts.app')

@section('title', 'Profil Pengguna')

@section('page')
    <header id="header" class="relative flex min-h-[34rem] w-full items-end overflow-hidden px-4 pb-12 pt-32 lg:px-8">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-20 top-18 h-56 w-56 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute right-0 top-24 h-72 w-72 rounded-full bg-[var(--orange)]/18 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/2 h-32 w-[82%] -translate-x-1/2 rounded-full bg-white/12 blur-3xl"></div>
        </div>

        <div class="relative mx-auto grid w-full max-w-[1600px] gap-8 xl:grid-cols-[1.15fr_0.85fr]">
            <section class="space-y-6 text-white">
                <span
                    class="inline-flex w-fit rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white/90">
                    Portal Operasional K3L
                </span>

                <div class="space-y-4">
                    <h1 class="max-w-4xl text-4xl font-bold leading-tight text-white lg:text-6xl">
                        Profil Mahasiswa yang lebih ringkas, jelas, dan siap dipakai.
                    </h1>
                    <p class="max-w-3xl text-base leading-8 text-white/88 lg:text-xl">
                        Lihat identitas utama, kontak penting, ringkasan kontribusi pelaporan, dan aktivitas terbaru Anda
                        dalam satu tampilan yang lebih rapi.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('user.profile.edit') }}"
                        class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-bold text-[var(--primary-color)] shadow-[0_14px_30px_rgba(255,255,255,0.18)] transition hover:-translate-y-1">
                        Edit Profil
                    </a>
                    <a href="{{ route('user.activities.index') }}"
                        class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/16">
                        Lihat Aktivitas
                    </a>
                </div>
            </section>

            <section class="relative">
                <div class="frosted-panel overflow-hidden rounded-[2rem] p-6 shadow-[0_28px_80px_rgba(7,45,112,0.22)]">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                        <div class="relative mx-auto sm:mx-0">
                            <div
                                class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-[2rem] bg-gradient-to-br from-[var(--primary-color)] via-[#2d7be5] to-[#8dbbff] text-4xl font-bold text-white shadow-[0_20px_40px_rgba(10,77,179,0.28)]">
                                {{ strtoupper(substr($profileCard['name'], 0, 1)) }}
                            </div>
                            <span
                                class="absolute -bottom-2 -right-2 inline-flex h-9 w-9 items-center justify-center rounded-full border-4 border-white bg-[var(--green)]">
                                <span class="material-symbols-outlined text-base text-white">verified</span>
                            </span>
                        </div>

                        <div class="flex-1 text-center sm:text-left">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]/60">
                                Identitas Aktif</p>
                            <h2 class="mt-2 text-3xl font-bold text-slate-900">{{ $profileCard['name'] }}</h2>
                            <p class="mt-2 text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">
                                {{ $profileCard['identifier'] }}</p>
                            <p class="mt-3 text-base font-semibold leading-7 text-slate-600">{{ $profileCard['roleLabel'] }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-[1.4rem] bg-[var(--blue-low-opacity)]/65 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status Akun</p>
                            <p class="mt-2 text-lg font-bold text-[var(--primary-color)]">Mahasiswa Aktif</p>
                        </div>
                        <div class="rounded-[1.4rem] bg-[#eef8f1] px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Progress K3L</p>
                            <p class="mt-2 text-lg font-bold text-[var(--green)]">{{ $closedIncidentCount }} laporan selesai
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </header>

    <main class="w-full bg-[#f6f8fc] pt-25 pb-25">
        <section class="w-full px-4 lg:px-8">
            <div class="mx-auto -mt-10 flex w-full max-w-[1600px] flex-col gap-6 lg:-mt-14">
                @include('partials.flash')

                <section class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
                    <article
                        class="section-shell overflow-hidden rounded-[2rem] p-6 shadow-[0_24px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/90 lg:p-8">
                        <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">
                            <div class="max-w-2xl">
                                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">
                                    Pusat Informasi</p>
                                <h3 class="mt-3 text-3xl font-bold text-slate-900 lg:text-4xl">Semua data penting Anda
                                    tersusun dalam satu panel.</h3>
                                <p class="mt-4 text-base leading-8 text-slate-600">
                                    Profil ini membantu Anda melihat identitas utama, kontak aktif, dan informasi pendukung
                                    agar proses pelaporan dan tindak lanjut lebih cepat.
                                </p>
                            </div>
                        </div>

                        <div class="grid w-full gap-3 sm:grid-cols-3">
                            @foreach ($stats as $stat)
                                <div
                                    class="flex min-h-[110px] w-full items-center rounded-[1.5rem] bg-white/88 px-5 py-5 shadow-[0_14px_30px_rgba(15,23,42,0.07)] ring-1 ring-slate-100">
                                    <div class="flex items-center gap-4">
                                        <span
                                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[var(--blue-low-opacity)] text-[var(--primary-color)]">
                                            <span class="material-symbols-outlined text-[1.7rem]">{{ $stat['icon'] }}</span>
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-2xl font-bold text-slate-900">{{ $stat['value'] }}</p>
                                            <p class="text-sm font-semibold text-slate-500">{{ $stat['label'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 grid gap-4 md:grid-cols-2">
                            <div
                                class="rounded-[1.6rem] bg-white px-5 py-5 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100">
                                <div class="flex items-start gap-4">
                                    <span
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#dce7ff] text-[var(--primary-color)]">
                                        <span class="material-symbols-outlined text-[1.6rem]">mail</span>
                                    </span>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Email</p>
                                        <p class="mt-2 break-all text-lg font-bold text-slate-900">
                                            {{ $profileCard['email'] }}</p>
                                        <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan email ini untuk menerima
                                            notifikasi pembaruan laporan.</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="rounded-[1.6rem] bg-white px-5 py-5 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100">
                                <div class="flex items-start gap-4">
                                    <span
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#dce7ff] text-[var(--primary-color)]">
                                        <span class="material-symbols-outlined text-[1.6rem]">call</span>
                                    </span>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Telepon
                                        </p>
                                        <p class="mt-2 text-lg font-bold text-slate-900">{{ $profileCard['phone'] }}</p>
                                        <p class="mt-2 text-sm leading-6 text-slate-500">Pastikan nomor aktif agar
                                            koordinasi darurat berjalan lebih cepat.</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="rounded-[1.6rem] bg-white px-5 py-5 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100">
                                <div class="flex items-start gap-4">
                                    <span
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#fff2df] text-[var(--orange)]">
                                        <span class="material-symbols-outlined text-[1.6rem]">badge</span>
                                    </span>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Identitas
                                        </p>
                                        <p class="mt-2 text-lg font-bold text-slate-900">{{ $profileCard['identifier'] }}
                                        </p>
                                        <p class="mt-2 text-sm leading-6 text-slate-500">Nomor identitas utama yang
                                            digunakan pada akun sistem Anda.</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="rounded-[1.6rem] bg-white px-5 py-5 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100">
                                <div class="flex items-start gap-4">
                                    <span
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#eef8f1] text-[var(--green)]">
                                        <span class="material-symbols-outlined text-[1.6rem]">health_and_safety</span>
                                    </span>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Info
                                            Medis</p>
                                        <p class="mt-2 text-lg font-bold text-slate-900">{{ $profileCard['medicalInfo'] }}
                                        </p>
                                        <p class="mt-2 text-sm leading-6 text-slate-500">Tambahkan informasi medis pada
                                            tahap berikutnya bila sistem sudah mendukung.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article
                        class="overflow-hidden rounded-[2rem] bg-[var(--primary-deep)] p-6 text-white shadow-[0_28px_60px_rgba(7,45,112,0.24)] ring-1 ring-white/10 lg:p-8">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-white/60">Ringkasan Profil
                                </p>
                                <h3 class="mt-3 text-3xl font-bold">Akun Anda siap dipakai untuk operasional K3L.</h3>
                            </div>
                            <span class="hidden p-4 items-center justify-center rounded-3xl bg-white/10 lg:flex">
                                <span class="material-symbols-outlined text-4xl text-white">shield_person</span>
                            </span>
                        </div>

                        <div class="mt-8 space-y-4">
                            <div class="rounded-[1.5rem] border border-white/10 bg-white/6 p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/55">Nama Pengguna</p>
                                <p class="mt-2 text-xl font-bold">{{ $profileCard['name'] }}</p>
                            </div>
                            <div class="rounded-[1.5rem] border border-white/10 bg-white/6 p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/55">Peran Akademik</p>
                                <p class="mt-2 text-xl font-bold">{{ $profileCard['roleLabel'] }}</p>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="rounded-[1.5rem] border border-white/10 bg-white/6 p-5">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/55">Total
                                        Aktivitas</p>
                                    <p class="mt-2 text-3xl font-bold">{{ $stats[0]['value'] }}</p>
                                    <p class="mt-2 text-sm leading-6 text-white/70">Gabungan laporan insiden dan hazard yang
                                        pernah Anda kirim.</p>
                                </div>
                                <div class="rounded-[1.5rem] border border-white/10 bg-white/6 p-5">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/55">Laporan
                                        Tuntas</p>
                                    <p class="mt-2 text-3xl font-bold">{{ $closedIncidentCount }}</p>
                                    <p class="mt-2 text-sm leading-6 text-white/70">Kasus yang telah selesai diproses dan
                                        ditutup.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 rounded-[1.6rem] bg-white px-5 py-5 text-slate-900">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--blue-low-opacity)] text-[var(--primary-color)]">
                                    <span class="material-symbols-outlined">tips_and_updates</span>
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-[var(--primary-color)]">Saran Cepat</p>
                                    <p class="text-sm leading-6 text-slate-500">Lengkapi nomor telepon dan rutin pantau
                                        aktivitas agar update laporan tidak terlewat.</p>
                                </div>
                            </div>
                        </div>
                    </article>
                </section>

                <section class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
                    <article
                        class="section-shell rounded-[2rem] p-6 shadow-[0_24px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/90 lg:p-8">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">
                                    Langkah Cepat</p>
                                <h3 class="mt-3 text-3xl font-bold text-slate-900">Akses yang paling sering Anda butuhkan.
                                </h3>
                            </div>
                        </div>

                        <div class="mt-8 grid gap-4">
                            <a href="{{ route('user.incidents.status') }}"
                                class="group flex items-center justify-between rounded-[1.5rem] bg-white px-5 py-5 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100 transition hover:-translate-y-1">
                                <div class="flex items-center gap-4">
                                    <span
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#dce7ff] text-[var(--primary-color)]">
                                        <span class="material-symbols-outlined">timeline</span>
                                    </span>
                                    <div>
                                        <p class="text-lg font-bold text-slate-900">Status Pelaporan</p>
                                        <p class="mt-1 text-sm text-slate-500">Pantau perkembangan seluruh laporan Anda.
                                        </p>
                                    </div>
                                </div>
                                <span
                                    class="material-symbols-outlined text-slate-400 transition group-hover:translate-x-1">arrow_forward</span>
                            </a>

                            <a href="{{ route('user.activities.index') }}"
                                class="group flex items-center justify-between rounded-[1.5rem] bg-white px-5 py-5 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100 transition hover:-translate-y-1">
                                <div class="flex items-center gap-4">
                                    <span
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef8f1] text-[var(--green)]">
                                        <span class="material-symbols-outlined">notifications</span>
                                    </span>
                                    <div>
                                        <p class="text-lg font-bold text-slate-900">Aktivitas Saya</p>
                                        <p class="mt-1 text-sm text-slate-500">Lihat pembaruan terbaru dari sistem dan
                                            laporan.</p>
                                    </div>
                                </div>
                                <span
                                    class="material-symbols-outlined text-slate-400 transition group-hover:translate-x-1">arrow_forward</span>
                            </a>

                            <a href="{{ route('user.knowledge.index') }}"
                                class="group flex items-center justify-between rounded-[1.5rem] bg-white px-5 py-5 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100 transition hover:-translate-y-1">
                                <div class="flex items-center gap-4">
                                    <span
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#fff2df] text-[var(--orange)]">
                                        <span class="material-symbols-outlined">menu_book</span>
                                    </span>
                                    <div>
                                        <p class="text-lg font-bold text-slate-900">Materi K3</p>
                                        <p class="mt-1 text-sm text-slate-500">Buka materi edukasi untuk memperkuat
                                            kesiapsiagaan.</p>
                                    </div>
                                </div>
                                <span
                                    class="material-symbols-outlined text-slate-400 transition group-hover:translate-x-1">arrow_forward</span>
                            </a>
                        </div>
                    </article>

                    <article
                        class="section-shell rounded-[2rem] p-6 shadow-[0_24px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/90 lg:p-8">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">
                                    Timeline Aktivitas</p>
                                <h3 class="mt-3 text-3xl font-bold text-slate-900">Riwayat terbaru yang terkait dengan akun
                                    Anda.</h3>
                            </div>
                            <p class="max-w-md text-sm leading-7 text-slate-500">Urutan ini menampilkan aktivitas laporan,
                                hazard, dan materi K3 terbaru yang paling relevan.</p>
                        </div>

                        <div class="mt-8 space-y-4">
                            @foreach ($timeline as $item)
                                <article
                                    class="relative rounded-[1.6rem] bg-white px-5 py-5 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100">
                                    <div class="flex gap-4">
                                        <div class="relative flex flex-col items-center">
                                            <span class="mt-1 p-2 rounded-full ring-4 ring-white"
                                                style="background-color: {{ $item['color'] }}"></span>
                                            @if (!$loop->last)
                                                <span class="mt-2 h-full w-px bg-slate-200"></span>
                                            @endif
                                        </div>
                                        <div class="flex-1 pb-2">
                                            <div
                                                class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                                <h4 class="text-xl font-bold text-slate-900">{{ $item['title'] }}</h4>
                                                <span
                                                    class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $item['meta'] }}</span>
                                            </div>
                                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $item['description'] }}
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </article>
                </section>
            </div>
        </section>
    </main>
@endsection
