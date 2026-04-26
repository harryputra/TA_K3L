@extends('user.layouts.app')

@section('title', 'Form Potensi Bahaya')

@section('page')
    <header id="header" class="flex h-135 w-full flex-col items-center justify-center gap-4 relative px-6">
        <div class="pointer-events-none absolute inset-x-0 bottom-8 mx-auto h-28 w-[82%] rounded-full bg-white/12 blur-3xl">
        </div>
        <div class="relative z-1 flex max-w-6xl flex-col items-center">
            <span
                class="inline-flex rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white/90">Portal
                Operasional K3L</span>
            <h1 class="mt-6 text-center text-5xl font-bold text-white lg:text-7xl">Pelaporan Potensi Bahaya</h1>
            <p class="max-w-6xl px-4 pt-2 text-center text-lg text-white/90 lg:text-2xl">
                Laporkan temuan kondisi tidak aman, near-miss, atau potensi risiko di area kampus agar dapat segera
                ditindaklanjuti sebelum menimbulkan insiden.
            </p>
        </div>
    </header>
    <main class="w-full bg-white pb-14">
        <section class="relative w-full overflow-hidden px-4 pb-26 pt-22 lg:px-8">
            <div class="mx-auto flex w-full max-w-[1600px] flex-col items-center">
                <div
                    class="relative z-10 w-full max-w-[1420px] rounded-[1.45rem] bg-white shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
                    <div class="border-b border-slate-300 px-14 py-12">
                        <h1 class="text-6xl font-bold leading-tight text-[var(--primary-color)]">Formulir Pelaporan Potensi
                            Bahaya (Near-Miss)</h1>
                        <p class="mt-4 max-w-4xl text-xl font-semibold leading-9 text-slate-600">
                            Bantu kami menjaga keamanan kampus. Laporkan temuan bahaya secepat mungkin agar dapat segera
                            ditangani oleh tim HSE POLMAN.
                        </p>
                    </div>

                    <form action="{{ route('user.hazards.store') }}" method="POST" enctype="multipart/form-data"
                        class="px-14 py-12">
                        @csrf
                        @include('partials.flash')
                        <div class="grid gap-10">
                            <div class="space-y-3">
                                <label for="hazard-title" class="block text-xl font-bold text-[var(--primary-color)]">Judul
                                    Temuan</label>
                                <input id="hazard-title" name="title" type="text" value="{{ old('title') }}"
                                    placeholder="Contoh: Kabel mesin CNC terkelupas"
                                    class="w-full rounded-[1rem] border-0 bg-[#eeeeef] px-6 py-5 text-lg font-semibold text-slate-700 outline-none placeholder:text-slate-400">
                            </div>

                            <div class="space-y-4">
                                <label class="block text-xl font-bold text-[var(--primary-color)]">Jenis Temuan</label>
                                <div class="grid gap-5 md:grid-cols-4">
                                    @foreach ($hazardTypes as $type)
                                        <label
                                            class="flex min-h-[112px] cursor-pointer flex-col items-center justify-center gap-3 rounded-[1rem] px-6 py-5 text-center shadow-[0_8px_18px_rgba(15,23,42,0.04)] {{ $selectedHazardType === $type['key'] ? 'bg-[var(--primary-color)] text-white' : 'bg-[#eeeeef] text-[var(--primary-color)]' }}">
                                            <input type="radio" name="hazard_type" value="{{ $type['key'] }}"
                                                class="sr-only" {{ $selectedHazardType === $type['key'] ? 'checked' : '' }}>
                                            <span class="material-symbols-outlined text-5xl">{{ $type['icon'] }}</span>
                                            <span class="text-2xl font-bold">{{ $type['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="grid gap-8 md:grid-cols-2">
                                <div class="space-y-3">
                                    <label for="hazard-location"
                                        class="block text-xl font-bold text-[var(--primary-color)]">Gedung /
                                        Laboratorium</label>
                                    <div class="flex items-center gap-4 rounded-[1rem] bg-[#eeeeef] px-6 py-5">
                                        <select id="hazard-location" name="location_id"
                                            class="w-full border-0 bg-transparent text-lg font-semibold text-[var(--primary-color)] outline-none">
                                            <option value="">Pilih lokasi</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>
                                                    {{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                        <span
                                            class="material-symbols-outlined text-[var(--primary-color)]">keyboard_arrow_down</span>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <label for="hazard-detail"
                                        class="block text-xl font-bold text-[var(--primary-color)]">Detail Spesifik</label>
                                    <input id="hazard-detail" name="specific_location" type="text"
                                        value="{{ old('specific_location') }}" placeholder="Lantai 2, M204"
                                        class="w-full rounded-[1rem] border-0 bg-[#eeeeef] px-6 py-5 text-lg font-semibold text-slate-700 outline-none placeholder:text-slate-400">
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label for="hazard-attachments"
                                    class="block text-xl font-bold text-[var(--primary-color)]">Unggah Foto Bukti</label>
                                <label for="hazard-attachments"
                                    class="flex min-h-[280px] cursor-pointer flex-col items-center justify-center gap-4 rounded-[1.1rem] border-2 border-dashed border-slate-400 bg-[#f2f2f2] px-6 py-8 text-center">
                                    <span
                                        class="flex h-26 w-26 items-center justify-center rounded-full bg-[var(--primary-color)]">
                                        <span class="material-symbols-outlined text-5xl text-white">photo_camera</span>
                                    </span>
                                    <div>
                                        <p class="text-2xl font-bold text-[var(--primary-color)]">Klik atau seret foto ke
                                            sini</p>
                                        <p class="text-lg font-semibold text-slate-700">Format JPG, PNG (Maksimal 5MB)</p>
                                    </div>
                                </label>
                                <input id="hazard-attachments" name="attachments[]" type="file" accept=".jpg,.jpeg,.png"
                                    multiple class="hidden">
                            </div>

                            <div class="space-y-3">
                                <label for="hazard-notes"
                                    class="block text-xl font-bold text-[var(--primary-color)]">Informasi Tambahan</label>
                                <textarea id="hazard-notes" name="notes" rows="5" placeholder="Berikan informasi tambahan jika diperlukan....."
                                    class="w-full rounded-[1.5rem] border-0 bg-[#eeeeef] px-6 py-6 text-lg font-semibold text-slate-700 outline-none placeholder:text-slate-400">{{ old('notes') }}</textarea>
                            </div>

                            <div class="rounded-[1rem] bg-[#f8fbff] px-6 py-5 ring-1 ring-[var(--primary-color)]/8">
                                <h3 class="text-xl font-bold text-[var(--primary-color)]">Panduan singkat sebelum mengirim
                                </h3>
                                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                    <div class="rounded-[0.9rem] bg-white px-4 py-4">
                                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">01</p>
                                        <p class="mt-2 text-sm leading-7 text-slate-700">Pastikan lokasi dan titik bahaya
                                            ditulis sejelas mungkin agar mudah ditemukan.</p>
                                    </div>
                                    <div class="rounded-[0.9rem] bg-white px-4 py-4">
                                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">02</p>
                                        <p class="mt-2 text-sm leading-7 text-slate-700">Unggah foto yang menunjukkan
                                            kondisi aktual, bukan foto umum area.</p>
                                    </div>
                                    <div class="rounded-[0.9rem] bg-white px-4 py-4">
                                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">03</p>
                                        <p class="mt-2 text-sm leading-7 text-slate-700">Jika bahaya berisiko langsung
                                            menimbulkan cedera, segera gunakan pusat darurat.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-center">
                                <button type="submit"
                                    class="inline-flex min-h-18 w-full items-center justify-center gap-3 rounded-full bg-[var(--primary-color)] px-10 text-2xl font-bold text-white">
                                    Kirim Laporan
                                    <span class="material-symbols-outlined text-3xl">arrow_right_alt</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div
                    class="relative z-10 mt-10 w-full max-w-[1420px] rounded-[1.1rem] bg-[#7a2c00] px-10 py-8 text-white shadow-[0_16px_35px_rgba(122,44,0,0.25)]">
                    <div class="flex items-start gap-6">
                        <span class="flex h-18 w-18 items-center justify-center rounded-full bg-[#d95b00]">
                            <span class="material-symbols-outlined text-4xl text-white">error</span>
                        </span>
                        <div class="space-y-2">
                            <h3 class="text-3xl font-bold">Keadaan Darurat?</h3>
                            <p class="max-w-5xl text-lg font-medium leading-8 text-white/90">
                                Jika temuan ini berpotensi langsung menyebabkan cedera atau kebakaran, segera gunakan tombol
                                darurat di dashboard untuk meminta bantuan cepat.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
