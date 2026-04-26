@csrf

<div class="grid gap-5">
    <div>
        <label for="title" class="mb-2 block text-sm font-semibold text-slate-800">Judul langkah</label>
        <input id="title" name="title" type="text" value="{{ old('title', $emergencyResponseStep->title ?? '') }}"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
    </div>
    <div>
        <label for="description" class="mb-2 block text-sm font-semibold text-slate-800">Deskripsi</label>
        <textarea id="description" name="description" rows="4"
            class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">{{ old('description', $emergencyResponseStep->description ?? '') }}</textarea>
    </div>
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="sort_order" class="mb-2 block text-sm font-semibold text-slate-800">Urutan</label>
            <input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $emergencyResponseStep->sort_order ?? 0) }}"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
        </div>
        <label class="flex items-center gap-3 pt-9 text-sm text-slate-700">
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-[var(--primary-color)] focus:ring-[var(--primary-color)]/30"
                @checked(old('is_active', $emergencyResponseStep->is_active ?? true))>
            Langkah aktif
        </label>
    </div>
    <div class="flex justify-end">
        <button type="submit" class="inline-flex rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
            {{ $submitLabel }}
        </button>
    </div>
</div>
