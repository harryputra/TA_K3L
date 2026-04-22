@extends('layouts.app')

@section('title', 'Form Pelaporan Insiden')

@section('content')
    <section class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
        <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Pelaporan Insiden</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Form Laporan Insiden K3L</h2>
            <p class="mt-4 text-sm leading-7 text-slate-600">
                Isi laporan secara lengkap agar Satgas dapat melakukan verifikasi dan tindak lanjut lebih cepat. Data yang akurat
                akan membantu proses investigasi dan pencegahan kejadian berulang.
            </p>

            <div class="mt-8 space-y-6 rounded-[1.5rem] bg-slate-900 p-6 text-sm text-slate-200">
                <div>
                    <p class="font-semibold text-amber-300">Panduan singkat</p>
                    <p class="mt-2 leading-7">Sertakan kronologi yang runtut, lokasi spesifik, dan kondisi korban atau lingkungan setelah kejadian.</p>
                </div>
                <div>
                    <p class="font-semibold text-amber-300">Lampiran yang disarankan</p>
                    <p class="mt-2 leading-7">Foto area kejadian, bukti kondisi alat, atau dokumen pendukung lain dengan ukuran maksimal 5 MB per file.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('user.incidents.store') }}" method="POST" enctype="multipart/form-data" class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
            @csrf

            <div class="grid gap-5">
                <div>
                    <label for="title" class="mb-2 block text-sm font-semibold text-slate-800">Judul insiden</label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" placeholder="Contoh: Terpeleset di area laboratorium kimia"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="incident_category_id" class="mb-2 block text-sm font-semibold text-slate-800">Kategori insiden</label>
                        <select id="incident_category_id" name="incident_category_id"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('incident_category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="location_id" class="mb-2 block text-sm font-semibold text-slate-800">Lokasi kejadian</label>
                        <select id="location_id" name="location_id"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                            <option value="">Pilih lokasi</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    <div>
                        <label for="incident_date" class="mb-2 block text-sm font-semibold text-slate-800">Tanggal kejadian</label>
                        <input id="incident_date" name="incident_date" type="date" value="{{ old('incident_date') }}"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                    </div>

                    <div>
                        <label for="incident_time" class="mb-2 block text-sm font-semibold text-slate-800">Waktu kejadian</label>
                        <input id="incident_time" name="incident_time" type="time" value="{{ old('incident_time') }}"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                    </div>

                    <div>
                        <label for="severity_level" class="mb-2 block text-sm font-semibold text-slate-800">Tingkat keparahan</label>
                        <select id="severity_level" name="severity_level"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                            <option value="">Pilih tingkat</option>
                            @foreach ($severityOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('severity_level') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <span class="mb-3 block text-sm font-semibold text-slate-800">Korban kejadian</span>
                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-300 px-4 py-3">
                            <input type="radio" name="victim_type" value="self" class="h-4 w-4 text-cyan-700 focus:ring-cyan-600" @checked(old('victim_type', 'self') === 'self')>
                            <span class="text-sm text-slate-700">Saya sendiri</span>
                        </label>

                        <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-300 px-4 py-3">
                            <input type="radio" name="victim_type" value="other" class="h-4 w-4 text-cyan-700 focus:ring-cyan-600" @checked(old('victim_type') === 'other')>
                            <span class="text-sm text-slate-700">Orang lain</span>
                        </label>
                    </div>
                </div>

                <div id="victim-user-wrapper" class="@if (old('victim_type', 'self') !== 'other') hidden @endif">
                    <label for="victim_user_id" class="mb-2 block text-sm font-semibold text-slate-800">ID user korban</label>
                    <input id="victim_user_id" name="victim_user_id" type="number" value="{{ old('victim_user_id') }}"
                        placeholder="Isi jika korban adalah pengguna lain yang sudah terdaftar"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                </div>

                <div>
                    <label for="chronology" class="mb-2 block text-sm font-semibold text-slate-800">Kronologi kejadian</label>
                    <textarea id="chronology" name="chronology" rows="6" placeholder="Jelaskan kejadian secara runtut: apa yang terjadi, di mana, siapa yang terlibat, dan kondisi sesudah kejadian."
                        class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">{{ old('chronology') }}</textarea>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="cause" class="mb-2 block text-sm font-semibold text-slate-800">Penyebab awal</label>
                        <textarea id="cause" name="cause" rows="4" placeholder="Opsional"
                            class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">{{ old('cause') }}</textarea>
                    </div>

                    <div>
                        <label for="initial_action" class="mb-2 block text-sm font-semibold text-slate-800">Tindakan awal</label>
                        <textarea id="initial_action" name="initial_action" rows="4" placeholder="Contoh: area diamankan, korban dibawa ke klinik"
                            class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">{{ old('initial_action') }}</textarea>
                    </div>
                </div>

                <div>
                    <label for="impact" class="mb-2 block text-sm font-semibold text-slate-800">Dampak kejadian</label>
                    <textarea id="impact" name="impact" rows="4" placeholder="Opsional"
                        class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">{{ old('impact') }}</textarea>
                </div>

                <div>
                    <label for="attachments" class="mb-2 block text-sm font-semibold text-slate-800">Lampiran bukti</label>
                    <input id="attachments" name="attachments[]" type="file" multiple
                        class="block w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-600 file:mr-4 file:rounded-full file:border-0 file:bg-cyan-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-cyan-800">
                    <p class="mt-2 text-xs text-slate-500">Format yang diizinkan: JPG, PNG, PDF, DOC, DOCX. Maksimal 3 file.</p>
                    <ul id="selected-files" class="mt-3 space-y-2 text-sm text-slate-600"></ul>
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs leading-6 text-slate-500">
                        Dengan mengirim laporan ini, Anda menyatakan informasi yang diberikan sesuai kondisi yang Anda ketahui.
                    </p>

                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-700 px-6 py-3 text-sm font-semibold text-white transition hover:bg-cyan-800">
                        Kirim Laporan
                    </button>
                </div>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        const victimRadios = document.querySelectorAll('input[name="victim_type"]');
        const victimUserWrapper = document.getElementById('victim-user-wrapper');
        const attachmentInput = document.getElementById('attachments');
        const selectedFiles = document.getElementById('selected-files');

        const toggleVictimField = () => {
            const selected = document.querySelector('input[name="victim_type"]:checked')?.value;
            victimUserWrapper.classList.toggle('hidden', selected !== 'other');
        };

        const renderSelectedFiles = () => {
            selectedFiles.innerHTML = '';

            Array.from(attachmentInput.files).forEach((file) => {
                const item = document.createElement('li');
                item.className = 'rounded-2xl bg-slate-100 px-4 py-2';
                item.textContent = `${file.name} (${Math.ceil(file.size / 1024)} KB)`;
                selectedFiles.appendChild(item);
            });
        };

        victimRadios.forEach((radio) => radio.addEventListener('change', toggleVictimField));
        attachmentInput.addEventListener('change', renderSelectedFiles);

        toggleVictimField();
    </script>
@endpush
