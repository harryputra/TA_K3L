@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')
@section('hero_eyebrow', 'Dashboard Admin')
@section('hero_title', 'Pusat kendali sistem K3L kampus')
@section('hero_description', 'Kelola pengguna, master data, knowledge, hazard, dan performa operasional dari satu dashboard yang konsisten dengan tampilan portal pengguna.')

@section('content')
    @php
        $summaryCards = [
            ['label' => 'Total Pengguna', 'value' => $stats['total_users'], 'hint' => $stats['active_users'] . ' akun aktif di sistem.', 'icon' => 'groups', 'tone' => 'text-[var(--orange)]'],
            ['label' => 'Laporan Insiden', 'value' => $stats['incident_count'], 'hint' => $stats['submitted_incidents'] . ' menunggu review awal.', 'icon' => 'warning', 'tone' => 'text-[var(--red)]'],
            ['label' => 'Satgas Aktif', 'value' => $stats['satgas_count'], 'hint' => 'Personel verifikasi dan tindak lanjut.', 'icon' => 'shield_person', 'tone' => 'text-[var(--green)]'],
            ['label' => 'Master Data', 'value' => $stats['location_count'], 'hint' => $stats['role_count'] . ' role sistem tersedia.', 'icon' => 'dataset', 'tone' => 'text-[var(--primary-color)]'],
            ['label' => 'Materi K3', 'value' => $stats['published_knowledge'], 'hint' => 'Konten pembelajaran yang sudah dipublikasikan.', 'icon' => 'menu_book', 'tone' => 'text-[var(--green)]'],
            ['label' => 'Hazard Report', 'value' => $stats['hazard_reports'], 'hint' => $stats['reviewed_hazards'] . ' ditinjau, ' . $stats['resolved_hazards'] . ' selesai.', 'icon' => 'report', 'tone' => 'text-[var(--yellow)]'],
        ];
    @endphp

    <section class="grid gap-6">
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
            @foreach ($summaryCards as $card)
                <article class="ambient-card rounded-[1.7rem] px-6 py-6 shadow-[0_18px_40px_rgba(15,23,42,0.08)] ring-1 ring-white/80">
                    <div class="flex items-start justify-between gap-4">
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-[0_12px_24px_rgba(15,23,42,0.08)]">
                            <span class="material-symbols-outlined text-3xl {{ $card['tone'] }}">{{ $card['icon'] }}</span>
                        </span>
                        <p class="text-4xl font-bold text-[var(--primary-color)]">{{ $card['value'] }}</p>
                    </div>
                    <h3 class="mt-6 text-lg font-bold text-[var(--primary-color)]">{{ $card['label'] }}</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">{{ $card['hint'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
            <div class="grid gap-6">
                <div class="section-shell rounded-[2rem] p-6 shadow-[0_24px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/90 lg:p-8">
                    <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Akses Cepat</p>
                            <h3 class="mt-3 text-3xl font-bold text-slate-900">Kelola modul utama sistem admin</h3>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600">
                                Gunakan shortcut ini untuk mengelola lokasi, kategori, konten knowledge, dan pusat data operasional lainnya.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <a href="{{ route('admin.users.index') }}"
                            class="flex min-h-[7rem] items-center gap-4 rounded-[1.5rem] bg-[var(--green)] px-5 py-4 text-white shadow-[0_16px_32px_rgba(21,153,71,0.24)] transition hover:-translate-y-1">
                            <span class="material-symbols-outlined text-4xl">manage_accounts</span>
                            <span class="text-lg font-bold leading-6">Kelola Akun</span>
                        </a>
                        <a href="{{ route('admin.locations.index') }}"
                            class="flex min-h-[7rem] items-center gap-4 rounded-[1.5rem] bg-[var(--primary-color)] px-5 py-4 text-white shadow-[0_16px_32px_rgba(10,77,179,0.22)] transition hover:-translate-y-1">
                            <span class="material-symbols-outlined text-4xl">pin_drop</span>
                            <span class="text-lg font-bold leading-6">Kelola Lokasi</span>
                        </a>
                        <a href="{{ route('admin.incident-categories.index') }}"
                            class="flex min-h-[7rem] items-center gap-4 rounded-[1.5rem] bg-[var(--yellow)] px-5 py-4 text-white shadow-[0_16px_32px_rgba(231,170,20,0.24)] transition hover:-translate-y-1">
                            <span class="material-symbols-outlined text-4xl">category</span>
                            <span class="text-lg font-bold leading-6">Kategori Insiden</span>
                        </a>
                        <a href="{{ route('admin.knowledge-articles.index') }}"
                            class="flex min-h-[7rem] items-center gap-4 rounded-[1.5rem] bg-[#123974] px-5 py-4 text-white shadow-[0_16px_32px_rgba(18,57,116,0.24)] transition hover:-translate-y-1">
                            <span class="material-symbols-outlined text-4xl">menu_book</span>
                            <span class="text-lg font-bold leading-6">Kelola Knowledge</span>
                        </a>
                        <a href="{{ route('admin.emergency-contacts.index') }}"
                            class="flex min-h-[7rem] items-center gap-4 rounded-[1.5rem] bg-[var(--red)] px-5 py-4 text-white shadow-[0_16px_32px_rgba(217,63,51,0.24)] transition hover:-translate-y-1">
                            <span class="material-symbols-outlined text-4xl">emergency_home</span>
                            <span class="text-lg font-bold leading-6">Emergency Center</span>
                        </a>
                    </div>
                </div>

                <div class="section-shell rounded-[2rem] p-6 shadow-[0_24px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/90 lg:p-8">
                    <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Laporan Terkini</p>
                            <h3 class="mt-3 text-3xl font-bold text-slate-900">Pantau aktivitas pelaporan terbaru</h3>
                        </div>
                        <p class="max-w-xl text-sm leading-7 text-slate-500">Admin dapat memonitor pelapor, lokasi kejadian, dan status proses untuk memastikan alur K3L berjalan rapi.</p>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-[1.6rem] border border-slate-100 shadow-all">
                        <table class="min-w-full table-auto border-separate border-spacing-0 text-sm">
                            <thead>
                                <tr class="bg-[var(--primary-color)] text-white">
                                    <th class="px-6 py-4 text-left font-semibold">No. Laporan</th>
                                    <th class="px-6 py-4 text-left font-semibold">Pelapor</th>
                                    <th class="px-6 py-4 text-left font-semibold">Lokasi</th>
                                    <th class="px-6 py-4 text-left font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @forelse ($recentReports as $report)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border-b border-gray-100 px-6 py-5 font-bold text-[var(--primary-color)]">{{ $report->report_number }}</td>
                                        <td class="border-b border-gray-100 px-6 py-5">{{ $report->reporter?->name ?? '-' }}</td>
                                        <td class="border-b border-gray-100 px-6 py-5">{{ $report->location?->name ?? '-' }}</td>
                                        <td class="border-b border-gray-100 px-6 py-5 text-slate-500">{{ str_replace('_', ' ', $report->status) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-slate-500">Belum ada laporan terbaru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="section-shell rounded-[2rem] p-6 shadow-[0_24px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/90 lg:p-8">
                    <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Hazard Terkini</p>
                            <h3 class="mt-3 text-3xl font-bold text-slate-900">Pantau temuan potensi bahaya terbaru</h3>
                        </div>
                        <p class="max-w-xl text-sm leading-7 text-slate-500">Feed ini membantu admin melihat hazard yang baru masuk, sudah ditinjau, atau sudah selesai ditangani.</p>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-[1.6rem] border border-slate-100 shadow-all">
                        <table class="min-w-full table-auto border-separate border-spacing-0 text-sm">
                            <thead>
                                <tr class="bg-[var(--yellow)] text-white">
                                    <th class="px-6 py-4 text-left font-semibold">No. Laporan</th>
                                    <th class="px-6 py-4 text-left font-semibold">Judul</th>
                                    <th class="px-6 py-4 text-left font-semibold">Lokasi</th>
                                    <th class="px-6 py-4 text-left font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @forelse ($recentHazardReports as $report)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border-b border-gray-100 px-6 py-5 font-bold text-[var(--primary-color)]">{{ $report->report_number }}</td>
                                        <td class="border-b border-gray-100 px-6 py-5">{{ $report->title }}</td>
                                        <td class="border-b border-gray-100 px-6 py-5">{{ $report->location?->name ?? '-' }}</td>
                                        <td class="border-b border-gray-100 px-6 py-5 text-slate-500">{{ str_replace('_', ' ', $report->status) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-slate-500">Belum ada hazard report terbaru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <aside class="grid gap-6">
                <div class="rounded-[2rem] bg-[var(--primary-color)] p-6 text-white shadow-[0_24px_55px_rgba(10,77,179,0.24)] lg:p-8">
                    <div class="rounded-[1.7rem] bg-white p-6 text-slate-900">
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Prioritas Admin</p>
                        <h3 class="mt-3 text-3xl font-bold">Fokus pengelolaan hari ini</h3>
                        <div class="mt-6 space-y-4 text-sm leading-7 text-slate-600">
                            <p>Pastikan data role, lokasi, dan kategori insiden tetap sinkron dengan kebutuhan operasional.</p>
                            <p>{{ $operationalSummary['incident_backlog'] }} insiden dan {{ $operationalSummary['hazard_backlog'] }} hazard masih berada di jalur tindak lanjut operasional.</p>
                            <p>Gunakan dashboard ini sebagai titik pantau utama untuk kualitas data dan konsistensi akses pengguna.</p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-[1.7rem] bg-white/10 p-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-white/75">Ringkasan Sistem</p>
                        <div class="mt-5 space-y-4">
                            <div class="flex items-center justify-between rounded-2xl bg-white/10 px-4 py-3">
                                <span class="text-sm">Akun aktif</span>
                                <span class="font-bold">{{ $stats['active_users'] }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-white/10 px-4 py-3">
                                <span class="text-sm">Submitted</span>
                                <span class="font-bold">{{ $stats['submitted_incidents'] }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-white/10 px-4 py-3">
                                <span class="text-sm">Lokasi</span>
                                <span class="font-bold">{{ $stats['location_count'] }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-white/10 px-4 py-3">
                                <span class="text-sm">Materi K3</span>
                                <span class="font-bold">{{ $stats['published_knowledge'] }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-white/10 px-4 py-3">
                                <span class="text-sm">Hazard report</span>
                                <span class="font-bold">{{ $stats['hazard_reports'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-shell rounded-[2rem] p-6 shadow-[0_24px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/90 lg:p-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Status Operasional</p>
                    <div class="mt-6 space-y-4">
                        <div class="flex items-center justify-between rounded-[1.2rem] bg-white px-4 py-4 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100">
                            <span class="text-sm font-semibold text-slate-500">Backlog insiden</span>
                            <span class="text-lg font-bold text-[var(--primary-color)]">{{ $operationalSummary['incident_backlog'] }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-[1.2rem] bg-white px-4 py-4 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100">
                            <span class="text-sm font-semibold text-slate-500">Backlog hazard</span>
                            <span class="text-lg font-bold text-[var(--primary-color)]">{{ $operationalSummary['hazard_backlog'] }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-[1.2rem] bg-white px-4 py-4 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100">
                            <span class="text-sm font-semibold text-slate-500">Materi published</span>
                            <span class="text-lg font-bold text-[var(--primary-color)]">{{ $operationalSummary['published_knowledge'] }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-[1.2rem] bg-white px-4 py-4 shadow-[0_12px_28px_rgba(15,23,42,0.06)] ring-1 ring-slate-100">
                            <span class="text-sm font-semibold text-slate-500">Kontak darurat aktif</span>
                            <span class="text-lg font-bold text-[var(--primary-color)]">{{ $operationalSummary['active_emergency_contacts'] }}</span>
                        </div>
                    </div>
                </div>
            </aside>
        </section>
    </section>
@endsection
