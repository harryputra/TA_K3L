@extends('user.layouts.app')

@section('title', 'Dashboard Pengguna')

@section('page')
    @php
        $statCards = [
            [
                'label' => 'Total Laporan Saya',
                'value' => $stats['my_reports'],
                'hint' => $stats['submitted_reports'] . ' menunggu review',
                'icon' => 'warning',
                'color' => 'text-[var(--orange)]',
            ],
            [
                'label' => 'Menunggu Review',
                'value' => $stats['submitted_reports'],
                'hint' => 'Sedang ditinjau Satgas',
                'icon' => 'schedule',
                'color' => 'text-[var(--yellow)]',
            ],
            [
                'label' => 'Sudah Diverifikasi',
                'value' => $stats['verified_reports'],
                'hint' => 'Lolos pemeriksaan awal',
                'icon' => 'verified',
                'color' => 'text-[var(--green)]',
            ],
            [
                'label' => 'Konten K3 Tersedia',
                'value' => $publishedKnowledgeCount,
                'hint' => 'Materi yang dipublikasikan',
                'icon' => 'book_5',
                'color' => 'text-[var(--primary-color)]',
            ],
            [
                'label' => 'Hazard Report Saya',
                'value' => $stats['my_hazards'],
                'hint' => $stats['resolved_hazards'] . ' sudah selesai',
                'icon' => 'emergency_home',
                'color' => 'text-[var(--yellow)]',
            ],
        ];

        $visibleReports = $recentReports->take(10);
        $visibleCount = $visibleReports->count();
        $totalReports = $stats['my_reports'];
        $latestReport = $latestReportSummary['report'];
        $latestHazard = $latestHazardSummary['report'];
    @endphp

        <header id="header" class="flex h-135 w-full flex-col items-center justify-center gap-4 relative px-6">
            <div class="pointer-events-none absolute inset-x-0 bottom-8 mx-auto h-28 w-[82%] rounded-full bg-white/12 blur-3xl"></div>
            <div class="relative z-1 flex max-w-6xl flex-col items-center">
            <span class="inline-flex rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white/90">Portal Operasional K3L</span>
            <h1 class="mt-6 text-center text-5xl font-bold text-white lg:text-7xl">Pusat Pelaporan & Edukasi K3 Polman</h1>
            <p class="max-w-6xl px-4 pt-2 text-center text-lg text-white/90 lg:text-2xl">
                Wujudkan lingkungan kampus yang aman. Gunakan portal ini untuk melaporkan potensi bahaya dan mempelajari
                standar keselamatan kerja.
            </p>
            </div>
        </header>
        <main class="w-full flex flex-col items-center">
            <section
                class="w-full h-fit bg-[var(--primary-deep)] px-6 py-12 pb-30 flex flex-row flex-wrap items-center justify-center gap-6 xl:px-10">
                @foreach ($statCards as $card)
                    <div class="ambient-card flex min-w-[260px] flex-1 flex-col items-start justify-center gap-3 rounded-[1.4rem] px-5 py-5 ring-1 ring-white/70 xl:max-w-[320px]">
                        <div class="flex items-center justify-center rounded-2xl bg-white p-4 shadow-[0_12px_24px_rgba(15,23,42,0.08)]">
                            <span class="material-symbols-outlined text-5xl {{ $card['color'] }}">
                                {{ $card['icon'] }}
                            </span>
                        </div>
                        <h6 class="text-xl text-[var(--primary-color)] font-semibold">{{ $card['label'] }}</h6>
                        <h1 class="text-3xl font-bold text-[var(--primary-color)]">{{ $card['value'] }}</h1>
                        <span class="font-semibold">{{ $card['hint'] }}</span>
                    </div>
                @endforeach
            </section>
            <section class="relative z-10 -mt-18 grid w-full grid-cols-1 gap-5 rounded-t-[2.6rem] bg-white p-6 xl:grid-cols-5 xl:px-8 xl:py-8">
                <div class="section-shell col-span-3 flex flex-col items-center justify-start gap-5 rounded-[1.8rem] p-4 shadow-2xl ring-1 ring-slate-200 xl:p-7">
                    <div class="mb-2 grid w-full grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <a href="{{ route('user.emergency.index') }}" class="flex h-26 flex-row items-center justify-center gap-3 rounded-[1.4rem] bg-[var(--red)] p-6 shadow-[0_16px_32px_rgba(217,63,51,0.24)] transition hover:-translate-y-1">
                            <span class="material-symbols-outlined icon-medium text-white">
                                health_metrics
                            </span>
                            <h2 class="font-bold text-white text-xl">Pusat Darurat</h2>
                        </a>
                        <a href="{{ route('user.hazards.create') }}" class="flex h-26 flex-row items-center justify-center gap-3 rounded-[1.4rem] bg-[var(--yellow)] p-6 shadow-[0_16px_32px_rgba(231,170,20,0.24)] transition hover:-translate-y-1">
                            <span class="material-symbols-outlined icon-medium text-white">
                                emergency_home
                            </span>
                            <h2 class="font-bold text-white text-xl">Form Potensi Bahaya</h2>
                        </a>
                        <a href="{{ route('user.incidents.create') }}" class="flex h-26 flex-row items-center justify-center gap-3 rounded-[1.4rem] bg-[var(--primary-color)] p-6 shadow-[0_16px_32px_rgba(10,77,179,0.22)] transition hover:-translate-y-1">
                            <span class="material-symbols-outlined icon-medium text-white">
                                contract_edit
                            </span>
                            <h2 class="font-bold text-white text-xl">Form Insiden</h2>
                        </a>
                        <a href="{{ route('user.knowledge.index') }}" class="flex h-26 flex-row items-center justify-center gap-3 rounded-[1.4rem] bg-[#123974] p-6 shadow-[0_16px_32px_rgba(18,57,116,0.24)] transition hover:-translate-y-1">
                            <span class="material-symbols-outlined icon-medium text-white">
                                book_5
                            </span>
                            <h2 class="font-bold text-white text-xl">Materi K3</h2>
                        </a>
                    </div>
                    <span class="w-full bg-gray-300 h-1 rounded-full"></span>
                    <div class="flex flex-row items-center justify-between w-full">
                        <h2 class="font-bold text-2xl">Tabel Laporan Terkini</h2>
                        <a href="{{ route('user.incidents.index') }}" class="bg-[var(--primary-color)] flex flex-row items-center justify-center gap-2 px-6 py-4 rounded-xl">
                            <h6 class="font-bold text-white">Lihat Semua</h6>
                            <span class="material-symbols-outlined text-white">
                                arrow_right_alt
                            </span>
                        </a>
                    </div>
                    <div class="w-full overflow-hidden rounded-[1.6rem] border border-gray-100 shadow-all">
                        <table class="w-full table-auto border-separate border-spacing-0">
                            <thead>
                                <tr class="bg-[var(--primary-color)] text-white">
                                    <th class="px-6 py-4 text-left text-sm font-semibold">No</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Kategori Laporan</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Lokasi Insiden</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @forelse ($visibleReports as $report)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-5 font-bold text-[var(--primary-color)] border-b border-gray-100">
                                            {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="px-6 py-5 border-b border-gray-100">
                                            {{ $report->category?->name ?? $report->title }}
                                        </td>
                                        <td class="px-6 py-5 border-b border-gray-100">{{ $report->location?->name ?? '-' }}</td>
                                        <td class="px-6 py-5 text-gray-500 border-b border-gray-100">
                                            {{ optional($report->submitted_at ?? $report->created_at)->format('d M Y, H.i') }} WIB
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada laporan yang dikirim.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="w-full flex items-center justify-between rounded-[1.4rem] bg-[#f5f8fc] px-6 py-4">
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <span>Menampilkan</span>
                            <span class="font-bold text-[var(--primary-color)]">{{ $visibleCount > 0 ? '1-' . $visibleCount : '0' }}</span>
                            <span>dari</span>
                            <span class="font-bold text-[var(--primary-color)]">{{ $totalReports }}</span>
                            <span>laporan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                class="flex h-11 w-11 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-400 shadow-sm cursor-pointer">
                                <span class="material-symbols-outlined">
                                    chevron_left
                                </span>
                            </button>
                            <button
                                class="flex h-11 min-w-11 items-center justify-center rounded-xl bg-[var(--primary-color)] px-4 font-bold text-white shadow-md cursor-pointer">
                                1
                            </button>
                            <button
                                class="flex h-11 w-11 items-center justify-center rounded-xl border border-gray-200 bg-white text-[var(--primary-color)] shadow-sm cursor-pointer hover:bg-gray-100">
                                <span class="material-symbols-outlined">
                                    chevron_right
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div
                    class="col-span-2 flex flex-col items-center justify-center gap-5 rounded-[1.9rem] bg-[var(--primary-color)]/97 p-5 shadow-[0_24px_55px_rgba(10,77,179,0.24)] xl:p-7">
                    <div
                        id="status-pelaporan"
                        class="ambient-card w-full flex flex-col items-center justify-center gap-4 rounded-[1.6rem] p-6 shadow-lg ring-1 ring-white/70 xl:p-8">
                        <h2 class="w-full text-start font-bold text-[var(--primary-color)] text-2xl">Status Pelaporan
                        </h2>
                        @if ($latestReport)
                            <div
                                class="mb-6 flex w-full grid grid-cols-2 flex-row items-center justify-between gap-4 rounded-[1.3rem] bg-white p-4 shadow-all">
                                <div class="col-span-1 flex flex-col items-start justify-center gap-3">
                                    <h6 class="font-bold">
                                        {{ optional($latestReport->submitted_at ?? $latestReport->created_at)->format('d M Y, H.i') . ' WIB' }}
                                    </h6>
                                    <div
                                        class="flex flex-row items-center justify-center bg-gray-200 p-2 pr-5 rounded-full shadow-lg gap-4">
                                        <span
                                            class="material-symbols-outlined text-3xl text-white bg-[var(--green)] rounded-full p-2">
                                            check_small
                                        </span>
                                        <h6 class="font-bold">{{ $latestReport->report_number }}</h6>
                                    </div>
                                </div>
                                <div
                                    class="title-pelaporan col-span-1 flex h-full items-center justify-center rounded-[1.2rem] p-4">
                                    <h6 class="font-bold text-white text-center text-xl">
                                        {{ ($latestReport->category?->name ?? 'Laporan Insiden') . ' - ' . $latestReport->title }}
                                    </h6>
                                </div>
                            </div>

                            <div class="w-full rounded-[1.3rem] bg-[#f8fbff] px-5 py-4 ring-1 ring-[var(--primary-color)]/10">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Status Terkini</p>
                                        <p class="mt-2 text-lg font-bold text-[var(--primary-color)]">{{ $latestReportSummary['status_label'] }}</p>
                                    </div>
                                    <span class="text-sm font-bold text-slate-500">{{ $latestReportSummary['progress_percent'] }}%</span>
                                </div>
                                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $latestReportSummary['status_note'] }}</p>
                            </div>
                        @else
                        @endif
                        @if ($latestReport)
                            <div
                                class="flex flex-row items-center gap-4 w-full progress-bar justify-between rounded-full px-10 py-5">
                                @foreach ($latestReportSummary['steps'] as $step)
                                    <span
                                        class="material-symbols-outlined text-3xl bg-white {{ $step['active'] ? 'text-[var(--green)]' : 'text-gray-300' }} rounded-full p-2">
                                        check_small
                                    </span>
                                @endforeach
                            </div>
                            <div class="mb-4 flex w-full flex-row items-center justify-center gap-9 rounded-full px-7">
                                @foreach ($latestReportSummary['steps'] as $step)
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <h6 class="{{ $step['active'] ? 'text-[var(--green)]' : 'text-gray-400' }} font-bold">
                                            {{ $step['number'] }}
                                        </h6>
                                        <h6 class="font-bold">{{ $step['label'] }}</h6>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mb-4 w-full rounded-[1.3rem] bg-[#f8fbff] px-5 py-5 ring-1 ring-[var(--primary-color)]/10">
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Informasi Status</p>
                                <p class="mt-3 text-xl font-bold text-[var(--primary-color)]">Belum ada laporan yang dikirim</p>
                                <p class="mt-3 text-sm leading-7 text-slate-600">
                                    Buat laporan pertama Anda untuk mulai memantau progres pelaporan secara real-time. Status dan tahapan penanganan akan muncul di sini setelah laporan berhasil masuk ke sistem.
                                </p>
                            </div>
                        @endif
                        <div
                            class="flex flex-row items-center justify-center gap-1 rounded-full border border-slate-300 bg-white px-4 py-2 text-slate-600">
                            <span class="text-[var(--green)]">
                                +
                            </span>
                            <h6 class="text-xs font-semibold">Info: Anda akan menerima notifikasi email jika status
                                laporan berubah.</h6>
                        </div>
                    </div>
                    <div
                        class="ambient-card w-full flex flex-col items-center justify-center gap-4 rounded-[1.6rem] p-6 shadow-lg ring-1 ring-white/70 xl:p-8">
                        <div class="flex w-full items-center justify-between gap-4">
                            <h2 class="text-start font-bold text-[var(--primary-color)] text-2xl">Status Hazard Terbaru</h2>
                            <a href="{{ route('user.hazards.index') }}" class="text-sm font-bold text-[var(--primary-color)]">Lihat Riwayat</a>
                        </div>
                        <div class="w-full rounded-[1.3rem] bg-[#f8fbff] px-5 py-4 ring-1 ring-[var(--primary-color)]/10">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Status Terkini</p>
                                    <p class="mt-2 text-lg font-bold text-[var(--primary-color)]">{{ $latestHazardSummary['status_label'] }}</p>
                                </div>
                                <span class="material-symbols-outlined rounded-full bg-[var(--yellow)]/15 p-3 text-[var(--yellow)]">warning</span>
                            </div>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $latestHazardSummary['status_note'] }}</p>
                        </div>
                        <div class="grid w-full grid-cols-2 gap-4 rounded-[1.3rem] bg-white">
                            <div class="rounded-[1.1rem] bg-[#f8fbff] px-4 py-4">
                                <p class="text-sm font-semibold text-slate-500">No. Hazard</p>
                                <p class="mt-2 text-lg font-bold text-[var(--primary-color)]">{{ $latestHazard?->report_number ?? 'Belum ada data' }}</p>
                            </div>
                            <div class="rounded-[1.1rem] bg-[#f8fbff] px-4 py-4">
                                <p class="text-sm font-semibold text-slate-500">Ditangani oleh</p>
                                <p class="mt-2 text-lg font-bold text-[var(--primary-color)]">{{ $latestHazardSummary['handled_by'] }}</p>
                            </div>
                        </div>
                        <div class="w-full rounded-[1.1rem] bg-[#f8fbff] px-4 py-4">
                            <p class="text-sm font-semibold text-slate-500">Temuan terakhir</p>
                            <p class="mt-2 text-base font-bold text-[var(--primary-color)]">{{ $latestHazard?->title ?? 'Belum ada hazard report yang dikirim' }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $latestHazard?->location?->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div
                        class="ambient-card w-full flex flex-col items-center justify-center gap-4 rounded-[1.6rem] p-6 shadow-xl ring-1 ring-white/70 xl:p-8">
                        <h2 class="w-full text-start font-bold text-[var(--primary-color)] text-2xl">Materi K3
                        </h2>
                        @if ($featuredKnowledge)
                            <div class="flex w-full flex-col items-start justify-center gap-4">
                                <div class="inline-flex rounded-full bg-[var(--primary-color)]/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-[var(--primary-color)]">
                                    {{ $featuredKnowledge->category?->name ?? 'Materi terbaru' }}
                                </div>
                                <div>
                                    <h3 class="text-black text-xl font-bold">{{ $featuredKnowledge->title }}</h3>
                                    <p class="mt-2 font-semibold text-black">
                                        {{ $featuredKnowledge->summary ?: 'Materi terbaru ini sudah dipublikasikan dan siap dipelajari.' }}
                                    </p>
                                </div>
                                <div class="flex w-full items-center justify-between rounded-2xl bg-[#f8fbff] px-4 py-4">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-500">Total materi tersedia</p>
                                        <p class="text-2xl font-bold text-[var(--primary-color)]">{{ $publishedKnowledgeCount }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-slate-500">Waktu baca</p>
                                        <p class="text-lg font-bold text-[var(--primary-color)]">{{ $featuredKnowledge->reading_time ?? '-' }}</p>
                                    </div>
                                </div>
                                @if ($knowledgeRecommendations->isNotEmpty())
                                    <div class="w-full space-y-3">
                                        @foreach ($knowledgeRecommendations as $knowledgeArticle)
                                            <a href="{{ route('user.knowledge.show', $knowledgeArticle->slug) }}"
                                                class="block rounded-2xl border border-slate-200 bg-[#f8fbff] px-4 py-4 transition hover:bg-white">
                                                <p class="font-bold text-[var(--primary-color)]">{{ $knowledgeArticle->title }}</p>
                                                <p class="mt-1 text-sm text-slate-500">{{ $knowledgeArticle->reading_time ?? '-' }}</p>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                                <a href="{{ route('user.knowledge.show', $featuredKnowledge->slug) }}"
                                    class="flex flex-row items-center justify-center w-full bg-[var(--primary-color)] text-white font-bold py-3 px-4 rounded-full">
                                    Buka Materi Terbaru
                                    <span class="material-symbols-outlined">
                                        arrow_right_alt
                                    </span>
                                </a>
                            </div>
                        @else
                            <div class="flex flex-col items-start justify-center w-full gap-4 mb-6">
                                <h3 class="font-bold text-black text-xl">Belum ada materi K3 yang dipublikasikan</h3>
                                <h3 class="font-semibold text-black">Materi akan muncul di sini setelah admin menambahkan dan mempublikasikannya.</h3>
                                <a href="{{ route('user.knowledge.index') }}"
                                    class="flex flex-row items-center justify-center w-full bg-[var(--primary-color)] text-white font-bold py-3 px-4 rounded-full">
                                    Ke Materi Pembelajaran
                                    <span class="material-symbols-outlined">
                                        arrow_right_alt
                                    </span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </main>
@endsection
