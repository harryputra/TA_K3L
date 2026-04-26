@extends('user.layouts.app')

@section('title', 'Form Pelaporan Insiden')

@section('page')
<header id="header" class="flex h-135 w-full flex-col items-center justify-center gap-4 relative px-6">
        <div class="pointer-events-none absolute inset-x-0 bottom-8 mx-auto h-28 w-[82%] rounded-full bg-white/12 blur-3xl">
        </div>
        <div class="relative z-1 flex max-w-6xl flex-col items-center">
            <span
                class="inline-flex rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white/90">Portal
                Operasional K3L</span>
            <h1 class="mt-6 text-center text-5xl font-bold text-white lg:text-7xl">Pelaporan Insiden</h1>
            <p class="max-w-6xl px-4 pt-2 text-center text-lg text-white/90 lg:text-2xl">
                Laporkan kejadian kecelakaan kerja, cedera, atau insiden operasional yang baru terjadi agar tim terkait
                dapat segera melakukan penanganan dan tindak lanjut.
            </p>
        </div>
    </header>
    <main class="w-full bg-white pb-14">
        <section class="relative w-full overflow-hidden px-4 pb-26 pt-22 lg:px-8">
            <div class="mx-auto flex w-full max-w-[1700px] flex-col items-center">
                <div class="relative z-10 w-full max-w-[1420px] rounded-[1.75rem] bg-white shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
                    <div class="border-b border-slate-300 px-14 py-12">
                        <h1 class="text-6xl font-bold leading-tight text-[var(--primary-color)]">Formulir Pelaporan Insiden / Kecelakaan</h1>
                        <p class="mt-4 max-w-4xl text-xl font-semibold leading-9 text-slate-600">
                            Segera laporkan kecelakaan atau kerusakan alat yang baru saja terjadi untuk penanganan lebih lanjut
                        </p>
                    </div>

                    <form action="{{ route('user.incidents.store') }}" method="POST" enctype="multipart/form-data" class="px-14 py-12">
                        @include('partials.flash')
                        @csrf
                        <input type="hidden" name="severity_level" value="{{ old('severity_level', 'medium') }}">
                        <input type="hidden" name="victim_type" value="{{ old('victim_type', 'self') }}">

                        <div class="grid gap-10">
                            <div class="grid gap-8 md:grid-cols-2">
                                <div class="space-y-3">
                                    <label for="incident_date" class="block text-xl font-bold text-[var(--primary-color)]">Tanggal & Jam Kejadian</label>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="flex items-center gap-4 rounded-[1rem] bg-[#eeeeef] px-6 py-5">
                                            <span class="material-symbols-outlined text-[var(--primary-color)]">calendar_today</span>
                                            <input id="incident_date" name="incident_date" type="date" value="{{ old('incident_date') }}"
                                                class="w-full border-0 bg-transparent text-lg font-semibold text-slate-700 outline-none">
                                        </div>
                                        <div class="flex items-center gap-4 rounded-[1rem] bg-[#eeeeef] px-6 py-5">
                                            <span class="material-symbols-outlined text-[var(--primary-color)]">schedule</span>
                                            <input id="incident_time" name="incident_time" type="time" value="{{ old('incident_time') }}"
                                                class="w-full border-0 bg-transparent text-lg font-semibold text-slate-700 outline-none">
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <label for="location_id" class="block text-xl font-bold text-[var(--primary-color)]">Lokasi Spesifik</label>
                                    <div class="flex items-center gap-4 rounded-[1rem] bg-[#eeeeef] px-6 py-5">
                                        <span class="material-symbols-outlined text-slate-400">location_on</span>
                                        <select id="location_id" name="location_id"
                                            class="w-full border-0 bg-transparent text-lg font-semibold text-slate-700 outline-none">
                                            <option value="">Pilih Gedung / Workshop</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="material-symbols-outlined text-[var(--primary-color)]">keyboard_arrow_down</span>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <label for="incident_category_id" class="block text-xl font-bold text-[var(--primary-color)]">Kategori Insiden</label>
                                    <div class="flex items-center gap-4 rounded-[1rem] bg-[#eeeeef] px-6 py-5">
                                        <select id="incident_category_id" name="incident_category_id"
                                            class="w-full border-0 bg-transparent text-lg font-semibold text-[var(--primary-color)] outline-none">
                                            <option value="">Pilih kategori</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" @selected(old('incident_category_id') == $category->id)>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="material-symbols-outlined text-[var(--primary-color)]">keyboard_arrow_down</span>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <label for="title" class="block text-xl font-bold text-[var(--primary-color)]">Kondisi Korban / Alat</label>
                                    <input id="title" name="title" type="text" value="{{ old('title') }}"
                                        placeholder="Contoh: Luka ringan di jari"
                                        class="w-full rounded-[1rem] border-0 bg-[#eeeeef] px-6 py-5 text-lg font-semibold text-slate-700 outline-none placeholder:text-slate-400">
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label for="chronology" class="block text-xl font-bold text-[var(--primary-color)]">Kronologi Kejadian</label>
                                <textarea id="chronology" name="chronology" rows="5" placeholder="Ceritakan bagaimana kecelakaan tersebut terjadi........"
                                    class="w-full rounded-[1.5rem] border-0 bg-[#eeeeef] px-6 py-6 text-lg font-semibold text-slate-700 outline-none placeholder:text-slate-400">{{ old('chronology') }}</textarea>
                            </div>

                            <div class="space-y-3">
                                <label for="initial_action" class="block text-xl font-bold text-[var(--primary-color)]">Tindakan P3K / Darurat yang Sudah Dilakukan</label>
                                <textarea id="initial_action" name="initial_action" rows="5" placeholder="Contoh: Korban sudah dibawa ke klinik kampus........"
                                    class="w-full rounded-[1.5rem] border-0 bg-[#eeeeef] px-6 py-6 text-lg font-semibold text-slate-700 outline-none placeholder:text-slate-400">{{ old('initial_action') }}</textarea>
                            </div>

                            <div class="space-y-3">
                                <label for="attachments" class="block text-xl font-bold text-[var(--primary-color)]">Unggah Foto Kondisi Terkini</label>
                                <label for="attachments"
                                    class="flex min-h-[280px] cursor-pointer flex-col items-center justify-center gap-4 rounded-[1.5rem] border-2 border-dashed border-slate-400 bg-[#f2f2f2] px-6 py-8 text-center">
                                    <span class="flex h-26 w-26 items-center justify-center rounded-full bg-[var(--primary-color)]">
                                        <span class="material-symbols-outlined text-5xl text-white">photo_camera</span>
                                    </span>
                                    <div>
                                        <p class="text-2xl font-bold text-[var(--primary-color)]">Klik atau seret foto ke sini</p>
                                        <p class="text-lg font-semibold text-slate-700">Format JPG, PNG (Maksimal 5MB)</p>
                                    </div>
                                </label>
                                <input id="attachments" name="attachments[]" type="file" multiple class="hidden">
                                <ul id="selected-files" class="space-y-2 text-base text-slate-600"></ul>
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

                <div class="relative z-10 mt-10 w-full max-w-[1420px] rounded-[1.5rem] bg-[#7a2c00] px-10 py-8 text-white shadow-[0_16px_35px_rgba(122,44,0,0.25)]">
                    <div class="flex items-start gap-6">
                        <span class="flex h-18 w-18 items-center justify-center rounded-full bg-[#d95b00]">
                            <span class="material-symbols-outlined text-4xl text-white">error</span>
                        </span>
                        <div class="space-y-2">
                            <h3 class="text-3xl font-bold">Keadaan Darurat?</h3>
                            <p class="max-w-5xl text-lg font-medium leading-8 text-white/90">
                                Jika terjadi kecelakaan dengan cedera fisik, segera hubungi Unit Kesehatan Kampus melalui tombol darurat di dashboard
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        const attachmentInput = document.getElementById('attachments');
        const selectedFiles = document.getElementById('selected-files');

        const renderSelectedFiles = () => {
            selectedFiles.innerHTML = '';

            Array.from(attachmentInput.files).forEach((file) => {
                const item = document.createElement('li');
                item.className = 'rounded-[1rem] bg-[#f2f2f2] px-5 py-3';
                item.textContent = `${file.name} (${Math.ceil(file.size / 1024)} KB)`;
                selectedFiles.appendChild(item);
            });
        };

        attachmentInput.addEventListener('change', renderSelectedFiles);
    </script>
@endpush
