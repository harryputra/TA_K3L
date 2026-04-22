@csrf

<div class="grid gap-5">
    <div>
        <label for="name" class="mb-2 block text-sm font-semibold text-slate-800">Nama lokasi</label>
        <input id="name" name="name" type="text" value="{{ old('name', $location->name ?? '') }}"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100"
            placeholder="Contoh: Laboratorium Kimia">
    </div>

    <div>
        <label for="code" class="mb-2 block text-sm font-semibold text-slate-800">Kode lokasi</label>
        <input id="code" name="code" type="text" value="{{ old('code', $location->code ?? '') }}"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100"
            placeholder="Contoh: LAB-KIM">
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-semibold text-slate-800">Deskripsi</label>
        <textarea id="description" name="description" rows="4"
            class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100"
            placeholder="Deskripsi singkat lokasi">{{ old('description', $location->description ?? '') }}</textarea>
    </div>

    <label class="flex items-center gap-3 text-sm text-slate-700">
        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600"
            @checked(old('is_active', $location->is_active ?? true))>
        Lokasi aktif dan dapat dipilih dalam pelaporan
    </label>

    <div class="flex justify-end">
        <button type="submit" class="inline-flex rounded-full bg-cyan-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-cyan-800">
            {{ $submitLabel }}
        </button>
    </div>
</div>
