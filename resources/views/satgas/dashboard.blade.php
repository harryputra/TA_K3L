@extends('layouts.dashboard')

@section('title', 'Dashboard Satgas')
@section('hero_eyebrow', 'Dashboard Satgas')
@section('hero_title', 'Ruang kerja verifikasi dan tindak lanjut insiden')
@section('hero_description', 'Fokuskan penanganan laporan prioritas, validasi temuan, dan koordinasikan respons lapangan dari satu tampilan yang seragam.')

@section('content')
    @php
        $summaryCards = [
            ['label' => 'Menunggu Verifikasi', 'value' => $stats['submitted_incidents'], 'hint' => 'Laporan yang perlu ditinjau sekarang.', 'icon' => 'fact_check', 'tone' => 'text-[var(--dashboard-orange)]'],
            ['label' => 'Sudah Diverifikasi', 'value' => $stats['verified_incidents'], 'hint' => 'Laporan yang lolos pengecekan awal.', 'icon' => 'check_circle', 'tone' => 'text-[var(--dashboard-green)]'],
            ['label' => 'Dalam Investigasi', 'value' => $stats['investigating_incidents'], 'hint' => 'Kasus yang perlu tindak lanjut lapangan.', 'icon' => 'travel_explore', 'tone' => 'text-[var(--dashboard-primary)]'],
            ['label' => 'Tindakan Selesai', 'value' => $stats['resolved_incidents'], 'hint' => 'Menunggu penutupan akhir atau konfirmasi.', 'icon' => 'assignment_turned_in', 'tone' => 'text-[var(--dashboard-primary)]'],
            ['label' => 'Prioritas Kritis', 'value' => $stats['critical_incidents'], 'hint' => 'Butuh perhatian dan koordinasi cepat.', 'icon' => 'emergency', 'tone' => 'text-[var(--dashboard-red)]'],
        ];

        $statusSteps = [
            ['label' => 'Masuk', 'active' => true],
            ['label' => 'Validasi', 'active' => true],
            ['label' => 'Investigasi', 'active' => $stats['investigating_incidents'] > 0],
            ['label' => 'Tindakan', 'active' => $stats['resolved_incidents'] > 0 || $stats['investigating_incidents'] > 0],
            ['label' => 'Selesai', 'active' => $stats['closed_incidents'] > 0],
        ];

        $severityColors = [
            'critical' => 'bg-[var(--dashboard-red)] text-white',
            'high' => 'bg-[var(--dashboard-orange)] text-white',
            'medium' => 'bg-[var(--dashboard-yellow)] text-slate-900',
            'low' => 'bg-emerald-100 text-emerald-800',
        ];
    @endphp

    <section class="space-y-8">
        <div class="rounded-[2rem] bg-[var(--dashboard-primary)] px-6 py-8 sm:px-8 sm:py-10">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                @foreach ($summaryCards as $card)
                    <article class="dashboard-shadow rounded-[1.75rem] bg-white p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div class="dashboard-glow flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50">
                                <span class="material-symbols-outlined text-3xl {{ $card['tone'] }}">{{ $card['icon'] }}</span>
                            </div>
                            <p class="text-3xl font-extrabold text-[var(--dashboard-primary)]">{{ $card['value'] }}</p>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-[var(--dashboard-primary)]">{{ $card['label'] }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $card['hint'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>

        <section class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
            <div class="dashboard-shadow rounded-[2rem] bg-white p-6 sm:p-8">
                <div class="grid gap-4 md:grid-cols-2">
                    <a href="{{ route('satgas.incidents.index') }}"
                        class="flex min-h-[7rem] items-center gap-4 rounded-[1.5rem] bg-[var(--dashboard-red)] px-5 py-4 text-white transition hover:opacity-95">
                        <span class="material-symbols-outlined text-4xl">siren</span>
                        <span class="text-lg font-bold leading-6">Tinjau laporan prioritas</span>
                    </a>
                    <div class="flex min-h-[7rem] items-center gap-4 rounded-[1.5rem] bg-[var(--dashboard-yellow)] px-5 py-4 text-white">
                        <span class="material-symbols-outlined text-4xl">route</span>
                        <span class="text-lg font-bold leading-6">Koordinasi verifikasi dan investigasi</span>
                    </div>
                </div>

                <div class="mt-8 flex flex-col gap-4 border-t border-slate-200 pt-8 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--dashboard-primary)]">Laporan Prioritas</p>
                        <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Daftar penanganan yang perlu perhatian</h3>
                    </div>
                    <p class="max-w-xl text-sm leading-7 text-slate-500">Urutan ini membantu Satgas fokus pada laporan kritis dan laporan yang masih berada di tahap investigasi aktif.</p>
                </div>

                <div class="mt-8 space-y-4">
                    @forelse ($priorityReports as $report)
                        <article class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-5">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <p class="text-lg font-bold text-slate-900">{{ $report->title }}</p>
                                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $severityColors[$report->severity_level] ?? 'bg-slate-200 text-slate-700' }}">
                                            {{ strtoupper($report->severity_level) }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-slate-500">
                                        {{ $report->report_number }} - {{ $report->reporter?->name ?? '-' }} - {{ $report->location?->name ?? '-' }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">Status: {{ str_replace('_', ' ', $report->status) }}</p>
                                </div>
                                <a href="{{ route('satgas.incidents.show', $report) }}"
                                    class="inline-flex items-center gap-2 rounded-full bg-[var(--dashboard-primary)] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#003b8a]">
                                    Tinjau laporan
                                    <span class="material-symbols-outlined text-[20px]">arrow_right_alt</span>
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-[1.75rem] border border-dashed border-slate-300 px-5 py-10 text-center text-sm text-slate-500">
                            Belum ada laporan prioritas untuk ditinjau.
                        </div>
                    @endforelse
                </div>
            </div>

            <aside class="dashboard-shadow rounded-[2rem] bg-[var(--dashboard-primary)] p-6 text-white sm:p-8">
                <div class="rounded-[1.75rem] bg-white p-6 text-slate-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--dashboard-primary)]">Alur Penanganan</p>
                    <h3 class="mt-3 text-2xl font-extrabold">Status proses pelaporan</h3>

                    <div class="mt-6 flex items-center justify-between gap-2 rounded-full bg-gradient-to-r from-[var(--dashboard-dark-green)] via-[var(--dashboard-green)] to-cyan-500 px-4 py-4">
                        @foreach ($statusSteps as $step)
                            <span class="material-symbols-outlined rounded-full bg-white p-2 text-2xl {{ $step['active'] ? 'text-[var(--dashboard-green)]' : 'text-slate-300' }}">
                                check_small
                            </span>
                        @endforeach
                    </div>

                    <div class="mt-4 grid grid-cols-5 gap-2 text-center text-xs font-bold text-slate-600">
                        @foreach ($statusSteps as $index => $step)
                            <div>
                                <p class="{{ $step['active'] ? 'text-[var(--dashboard-green)]' : 'text-slate-400' }}">{{ $index + 1 }}</p>
                                <p class="mt-1">{{ $step['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 rounded-[1.75rem] bg-white/10 p-6">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-white/75">Fokus Harian</p>
                    <div class="mt-5 space-y-4 text-sm leading-7 text-white/90">
                        <p>Tinjau {{ $workloadSummary['needs_review'] }} laporan prioritas yang masih menunggu review di antrean kerja saat ini.</p>
                        <p>{{ $workloadSummary['needs_field_follow_up'] }} laporan prioritas membutuhkan tindak lanjut lapangan atau konfirmasi progres.</p>
                        <p>{{ $workloadSummary['ready_to_close'] }} laporan sudah berada di tahap tindakan selesai dan siap dievaluasi untuk penutupan.</p>
                    </div>
                </div>
            </aside>
        </section>
    </section>
@endsection
