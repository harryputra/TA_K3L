@extends('layouts.app')

@section('title', 'Review Detail Insiden')

@section('content')
    <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-6">
            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Review Satgas</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">{{ $incidentReport->title }}</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">
                    Laporan dari <span class="font-semibold text-slate-900">{{ $incidentReport->reporter?->name ?? '-' }}</span>
                    dengan nomor <span class="font-semibold text-slate-900">{{ $incidentReport->report_number }}</span>.
                </p>
            </div>

            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Ringkasan Kejadian</h3>
                <div class="mt-6 grid gap-5 sm:grid-cols-2 text-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Kategori</p>
                        <p class="mt-2 text-slate-800">{{ $incidentReport->category?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Lokasi</p>
                        <p class="mt-2 text-slate-800">{{ $incidentReport->location?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tanggal</p>
                        <p class="mt-2 text-slate-800">{{ optional($incidentReport->incident_date)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</p>
                        <p class="mt-2 text-slate-800">{{ str_replace('_', ' ', $incidentReport->status) }}</p>
                    </div>
                </div>

                <div class="mt-8 space-y-5 text-sm leading-7 text-slate-700">
                    <div>
                        <p class="font-semibold text-slate-900">Kronologi</p>
                        <p class="mt-2">{{ $incidentReport->chronology }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Tindakan Awal</p>
                        <p class="mt-2">{{ $incidentReport->initial_action ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Dampak</p>
                        <p class="mt-2">{{ $incidentReport->impact ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Verifikasi Laporan</h3>
                <form action="{{ route('satgas.incidents.verify', $incidentReport) }}" method="POST" class="mt-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="verification_note" class="mb-2 block text-sm font-semibold text-slate-800">Catatan verifikasi</label>
                        <textarea id="verification_note" name="verification_note" rows="5"
                            class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100"
                            placeholder="Tambahkan catatan verifikasi, temuan awal, atau arahan tindak lanjut.">{{ old('verification_note') }}</textarea>
                    </div>

                    <button type="submit" class="inline-flex rounded-full bg-cyan-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-cyan-800">
                        Verifikasi Laporan
                    </button>
                </form>
            </div>

            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Riwayat Status</h3>
                <div class="mt-6 space-y-4">
                    @forelse ($incidentReport->statusHistories->sortByDesc('created_at') as $history)
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-sm font-semibold text-slate-900">{{ str_replace('_', ' ', $history->to_status) }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ optional($history->created_at)->format('d M Y H:i') }} oleh {{ $history->changer?->name ?? 'Sistem' }}
                            </p>
                            @if ($history->change_note)
                                <p class="mt-2 text-sm text-slate-700">{{ $history->change_note }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada riwayat status.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
