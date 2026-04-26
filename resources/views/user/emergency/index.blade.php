@extends('user.layouts.app')

@section('title', 'Pusat Darurat K3L')

@section('page')
    <header id="header" class="flex h-135 w-full flex-col items-center justify-center gap-4 relative px-6">
        <div class="pointer-events-none absolute inset-x-0 bottom-8 mx-auto h-28 w-[82%] rounded-full bg-white/12 blur-3xl">
        </div>
        <div class="relative z-1 flex flex-col gap-5 items-center">
            <span
                class="inline-flex rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white/90">Portal
                Operasional K3L</span>
            <h1 class="mt-6 text-center text-5xl font-bold text-white lg:text-7xl">Pusat Darurat & Respon Cepat K3L</h1>
            <p class="max-w-6xl px-4 pt-2 text-center text-lg text-white/90 lg:text-2xl">
                Akses langkah pertolongan pertama, kontak prioritas, dan panduan tindakan cepat agar Anda bisa merespons
            kejadian tanpa harus mencari informasi lebih dulu.
            </p>
        </div>
    </header>

    <main class="flex w-full flex-col items-center bg-gradient-to-b from-[#edf4ff] via-[#f7faff] to-[#eef5ff] pb-16">
        <section
            class="flex h-fit w-full flex-row flex-wrap items-center justify-center gap-10 bg-[var(--primary-color)] px-8 py-15 pb-30">
            @foreach ($emergencyContacts as $contact)
                <div
                    class="flex min-w-[300px] flex-col items-start justify-center gap-4 rounded-[1.25rem] bg-white/94 pr-14 pl-4 pt-4 pb-4 shadow-all ring-1 ring-white/70">
                    <div class="flex items-center justify-center rounded-xl p-4 shadow-lg {{ $contact->color_class }}">
                        <span class="material-symbols-outlined text-5xl text-white">
                            {{ $contact->icon }}
                        </span>
                    </div>
                    <h6 class="text-xl font-semibold text-[var(--primary-color)]">{{ $contact->name }}</h6>
                    <h1 class="text-3xl font-bold text-[var(--primary-color)]">{{ $contact->phone_number }}</h1>
                    <span class="font-semibold text-slate-600">{{ $contact->description }}</span>
                </div>
            @endforeach
        </section>

        <section class="relative z-10 -mt-14 flex w-full max-w-[1600px] flex-col gap-6 px-4 lg:px-8">
            <div class="grid gap-6 xl:grid-cols-[1.45fr_0.85fr]">
                <div
                    class="flex flex-col gap-6 rounded-[1.35rem] bg-white p-8 shadow-all ring-1 ring-[var(--primary-color)]/8 lg:p-10">
                    <div class="flex w-full flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-3xl font-bold text-[var(--primary-color)]">Langkah Tanggap Cepat</h2>
                            <p class="mt-2 max-w-3xl text-lg leading-8 text-slate-600">
                                Ikuti urutan ini sebelum masuk ke panduan pertolongan pertama per jenis kecelakaan.
                            </p>
                        </div>
                        <a href="{{ route('user.incidents.create') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-[1rem] bg-[var(--primary-color)] px-6 py-4">
                            <h6 class="font-bold text-white">Buat Laporan</h6>
                            <span class="material-symbols-outlined text-white">arrow_right_alt</span>
                        </a>
                    </div>

                    <div class="grid w-full gap-5">
                        @foreach ($responseSteps as $item)
                            <div
                                class="flex w-full flex-row items-start gap-4 rounded-[1rem] border border-gray-200 bg-[#f8fbff] p-6 shadow-all">
                                <div
                                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[var(--primary-color)] text-xl font-bold text-white">
                                    {{ $loop->iteration }}
                                </div>
                                <div class="flex flex-col gap-2">
                                    <h3 class="text-xl font-bold text-[var(--primary-color)]">{{ $item->title }}</h3>
                                    <p class="leading-7 text-slate-600">{{ $item->description }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col gap-6 rounded-[1.35rem] bg-white p-8 shadow-xl lg:p-10">
                    <div class="flex w-full flex-col gap-4 rounded-[1.1rem] bg-white/96 p-7 shadow-lg">
                        <h2 class="w-full text-start text-2xl font-bold text-[var(--primary-color)]">Kontak Prioritas</h2>
                        <div class="flex w-full flex-col gap-4">
                            @foreach ($emergencyContacts as $contact)
                                <div
                                    class="flex w-full items-center justify-between rounded-[0.95rem] border border-gray-200 bg-[#f8fbff] px-4 py-4">
                                    <div>
                                        <h4 class="font-bold text-[var(--primary-color)]">{{ $contact->name }}</h4>
                                        <p class="text-sm text-slate-500">{{ $contact->description }}</p>
                                    </div>
                                    <span class="font-bold text-[var(--primary-color)]">{{ $contact->phone_number }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex w-full flex-col gap-4 rounded-[1.1rem] bg-white/96 p-7 shadow-xl">
                        <h2 class="w-full text-start text-2xl font-bold text-[var(--primary-color)]">Riwayat Laporan
                            Terakhir</h2>
                        <div class="flex w-full flex-col gap-4">
                            @forelse ($recentReports as $report)
                                <a href="{{ route('user.incidents.show', $report) }}"
                                    class="w-full rounded-[0.95rem] border border-gray-200 bg-[#f8fbff] px-4 py-4 transition hover:bg-white">
                                    <h4 class="font-bold text-[var(--primary-color)]">{{ $report->title }}</h4>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $report->category?->name ?? '-' }} • {{ $report->location?->name ?? '-' }}
                                    </p>
                                </a>
                            @empty
                                <div
                                    class="w-full rounded-[0.95rem] border border-dashed border-gray-300 bg-[#f8fbff] px-4 py-6 text-center text-slate-500">
                                    Belum ada laporan yang tercatat.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <section class="rounded-[1.35rem] bg-white/96 p-8 shadow-all ring-1 ring-[var(--primary-color)]/8 lg:p-10">
                <div>
                    <h2 class="text-3xl font-bold text-[var(--primary-color)]">Pertolongan Pertama Berdasarkan Jenis
                        Kecelakaan</h2>
                    <p class="mt-2 max-w-4xl text-lg leading-8 text-slate-600">
                        Baca tindakan awal yang paling relevan dengan kondisi korban. Fokuskan pada langkah aman, cepat, dan
                        mudah diingat sebelum petugas tiba.
                    </p>
                </div>

                <div class="mt-8 grid gap-5 lg:grid-cols-2 2xl:grid-cols-3">
                    @foreach ($firstAidGuides as $guide)
                        <article
                            class="rounded-[1.1rem] border border-slate-200 bg-[#f8fbff] p-6 shadow-[0_8px_24px_rgba(15,23,42,0.06)]">
                            <div class="flex items-start gap-4">
                                <span
                                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full {{ $guide->accent_class }}">
                                    <span class="material-symbols-outlined text-3xl text-white">{{ $guide->icon }}</span>
                                </span>
                                <div>
                                    <h3 class="text-2xl font-bold text-[var(--primary-color)]">{{ $guide->title }}</h3>
                                    <p class="mt-2 text-base leading-7 text-slate-600">{{ $guide->summary }}</p>
                                </div>
                            </div>

                            <div class="mt-5 space-y-3">
                                @foreach ($guide->actions as $action)
                                    <div
                                        class="flex items-start gap-3 rounded-[0.9rem] bg-white px-4 py-3 ring-1 ring-slate-100">
                                        <span
                                            class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[var(--primary-color)]"></span>
                                        <p class="text-sm leading-7 text-slate-700">{{ $action->description }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        </section>
    </main>
@endsection
