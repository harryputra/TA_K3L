@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')
@section('hero_eyebrow', 'Dashboard Admin')
@section('hero_title', 'Pusat kendali akun dan layanan darurat')
@section('hero_description', 'Admin fokus mengelola akun, akses sistem, dan konten pusat darurat. Materi K3, insiden, dan lokasi operasional ditangani oleh Satgas.')

@section('content')
    @php
        $summaryCards = [
            ['label' => 'Total Pengguna', 'value' => $stats['total_users'], 'hint' => $stats['active_users'] . ' akun aktif di sistem.', 'icon' => 'groups', 'tone' => 'text-[var(--orange)]'],
            ['label' => 'Satgas Aktif', 'value' => $stats['satgas_count'], 'hint' => 'Personel verifikasi dan tindak lanjut.', 'icon' => 'shield_person', 'tone' => 'text-[var(--green)]'],
            ['label' => 'Role Sistem', 'value' => $stats['role_count'], 'hint' => 'Peran akses yang tersedia.', 'icon' => 'admin_panel_settings', 'tone' => 'text-[var(--primary-color)]'],
            ['label' => 'Kontak Darurat', 'value' => $stats['emergency_contacts'], 'hint' => 'Kontak aktif pada pusat darurat.', 'icon' => 'emergency_home', 'tone' => 'text-[var(--red)]'],
            ['label' => 'Hazard Report', 'value' => $stats['hazard_reports'], 'hint' => 'Data pantauan, bukan area kelola admin.', 'icon' => 'report', 'tone' => 'text-[var(--yellow)]'],
        ];
    @endphp

    <section class="grid gap-6">
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
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

        <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="grid gap-6">
                <div class="section-shell rounded-[2rem] p-6 shadow-[0_24px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/90 lg:p-8">
                    <div class="border-b border-slate-200 pb-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Akses Cepat</p>
                        <h3 class="mt-3 text-3xl font-bold text-slate-900">Modul yang dikelola admin</h3>
                        <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600">
                            Area admin dibatasi untuk akun dan pusat darurat. Teori/knowledge, insiden, dan lokasi operasional tidak dikelola dari panel admin.
                        </p>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <a href="{{ route('admin.users.index') }}"
                            class="flex min-h-[7rem] items-center gap-4 rounded-[1.5rem] bg-[var(--green)] px-5 py-4 text-white shadow-[0_16px_32px_rgba(21,153,71,0.24)] transition hover:-translate-y-1">
                            <span class="material-symbols-outlined text-4xl">manage_accounts</span>
                            <span class="text-lg font-bold leading-6">Kelola Akun</span>
                        </a>
                        <a href="{{ route('admin.emergency-contacts.index') }}"
                            class="flex min-h-[7rem] items-center gap-4 rounded-[1.5rem] bg-[var(--red)] px-5 py-4 text-white shadow-[0_16px_32px_rgba(217,63,51,0.24)] transition hover:-translate-y-1">
                            <span class="material-symbols-outlined text-4xl">emergency_home</span>
                            <span class="text-lg font-bold leading-6">Emergency Center</span>
                        </a>
                        <a href="{{ route('admin.hazards.index') }}"
                            class="flex min-h-[7rem] items-center gap-4 rounded-[1.5rem] bg-[var(--yellow)] px-5 py-4 text-white shadow-[0_16px_32px_rgba(231,170,20,0.24)] transition hover:-translate-y-1">
                            <span class="material-symbols-outlined text-4xl">visibility</span>
                            <span class="text-lg font-bold leading-6">Pantau Hazard</span>
                        </a>
                    </div>
                </div>

                <div class="section-shell rounded-[2rem] p-6 shadow-[0_24px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/90 lg:p-8">
                    <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Hazard Terkini</p>
                            <h3 class="mt-3 text-3xl font-bold text-slate-900">Pantauan temuan potensi bahaya</h3>
                        </div>
                        <p class="max-w-xl text-sm leading-7 text-slate-500">Admin hanya melihat ringkasan hazard; proses review, lokasi, dan tindak lanjut berada di area Satgas.</p>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-[1.6rem] border border-slate-100 shadow-all">
                        <table class="min-w-full table-auto border-separate border-spacing-0 text-sm">
                            <thead>
                                <tr class="bg-[var(--yellow)] text-white">
                                    <th class="px-6 py-4 text-left font-semibold">No. Laporan</th>
                                    <th class="px-6 py-4 text-left font-semibold">Judul</th>
                                    <th class="px-6 py-4 text-left font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @forelse ($recentHazardReports as $report)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border-b border-gray-100 px-6 py-5 font-bold text-[var(--primary-color)]">{{ $report->report_number }}</td>
                                        <td class="border-b border-gray-100 px-6 py-5">{{ $report->title }}</td>
                                        <td class="border-b border-gray-100 px-6 py-5 text-slate-500">{{ str_replace('_', ' ', $report->status) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 text-center text-slate-500">Belum ada hazard report terbaru.</td>
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
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Batas Peran Admin</p>
                        <h3 class="mt-3 text-3xl font-bold">Admin menjaga akses, bukan proses operasional lapangan.</h3>
                        <div class="mt-6 space-y-4 text-sm leading-7 text-slate-600">
                            <p>Teori/knowledge dikelola oleh Satgas melalui panel Satgas.</p>
                            <p>Insiden dan kategori konfirmasi insiden juga menjadi tanggung jawab Satgas.</p>
                            <p>Lokasi yang dipakai untuk hazard tidak dikelola dari panel admin.</p>
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
                                <span class="text-sm">Satgas</span>
                                <span class="font-bold">{{ $stats['satgas_count'] }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-white/10 px-4 py-3">
                                <span class="text-sm">Kontak darurat</span>
                                <span class="font-bold">{{ $stats['emergency_contacts'] }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-white/10 px-4 py-3">
                                <span class="text-sm">Hazard report</span>
                                <span class="font-bold">{{ $stats['hazard_reports'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </section>
    </section>
@endsection
