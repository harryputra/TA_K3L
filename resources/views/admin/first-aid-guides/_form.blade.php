@csrf

<div class="grid gap-5">
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="title" class="mb-2 block text-sm font-semibold text-slate-800">Judul panduan</label>
            <input id="title" name="title" type="text" value="{{ old('title', $firstAidGuide->title ?? '') }}"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
        </div>
        <div>
            <label for="icon" class="mb-2 block text-sm font-semibold text-slate-800">Icon</label>
            <input id="icon" name="icon" type="text" value="{{ old('icon', $firstAidGuide->icon ?? '') }}"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
        </div>
    </div>
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="accent_class" class="mb-2 block text-sm font-semibold text-slate-800">Accent class</label>
            <input id="accent_class" name="accent_class" type="text" value="{{ old('accent_class', $firstAidGuide->accent_class ?? '') }}"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
        </div>
        <div>
            <label for="sort_order" class="mb-2 block text-sm font-semibold text-slate-800">Urutan</label>
            <input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $firstAidGuide->sort_order ?? 0) }}"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
        </div>
    </div>
    <div>
        <label for="summary" class="mb-2 block text-sm font-semibold text-slate-800">Ringkasan</label>
        <textarea id="summary" name="summary" rows="3"
            class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">{{ old('summary', $firstAidGuide->summary ?? '') }}</textarea>
    </div>
    <div>
        <label for="actions_text" class="mb-2 block text-sm font-semibold text-slate-800">Daftar aksi</label>
        <textarea id="actions_text" name="actions_text" rows="8"
            class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
            placeholder="Satu baris untuk satu aksi">{{ old('actions_text', isset($firstAidGuide) ? $firstAidGuide->actions->pluck('description')->implode("\n") : '') }}</textarea>
    </div>
    <label class="flex items-center gap-3 text-sm text-slate-700">
        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-[var(--primary-color)] focus:ring-[var(--primary-color)]/30"
            @checked(old('is_active', $firstAidGuide->is_active ?? true))>
        Panduan aktif
    </label>
    <div class="flex justify-end">
        <button type="submit" class="inline-flex rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
            {{ $submitLabel }}
        </button>
    </div>
</div>
