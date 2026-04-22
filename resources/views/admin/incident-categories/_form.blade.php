@csrf

<div class="grid gap-5">
    <div>
        <label for="name" class="mb-2 block text-sm font-semibold text-slate-800">Nama kategori</label>
        <input id="name" name="name" type="text" value="{{ old('name', $incidentCategory->name ?? '') }}"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100"
            placeholder="Contoh: Near Miss">
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-semibold text-slate-800">Deskripsi</label>
        <textarea id="description" name="description" rows="4"
            class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100"
            placeholder="Deskripsi singkat kategori">{{ old('description', $incidentCategory->description ?? '') }}</textarea>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="inline-flex rounded-full bg-cyan-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-cyan-800">
            {{ $submitLabel }}
        </button>
    </div>
</div>
