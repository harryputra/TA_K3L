@csrf

<div class="grid gap-5">
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="name" class="mb-2 block text-sm font-semibold text-slate-800">Nama kontak</label>
            <input id="name" name="name" type="text" value="{{ old('name', $emergencyContact->name ?? '') }}"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
        </div>
        <div>
            <label for="phone_number" class="mb-2 block text-sm font-semibold text-slate-800">Nomor telepon</label>
            <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number', $emergencyContact->phone_number ?? '') }}"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
        </div>
    </div>
    <div>
        <label for="description" class="mb-2 block text-sm font-semibold text-slate-800">Deskripsi</label>
        <textarea id="description" name="description" rows="4"
            class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">{{ old('description', $emergencyContact->description ?? '') }}</textarea>
    </div>
    <div class="grid gap-5 md:grid-cols-3">
        <div>
            <label for="icon" class="mb-2 block text-sm font-semibold text-slate-800">Icon</label>
            <input id="icon" name="icon" type="text" value="{{ old('icon', $emergencyContact->icon ?? '') }}"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
        </div>
        <div>
            <label for="color_class" class="mb-2 block text-sm font-semibold text-slate-800">Color class</label>
            <input id="color_class" name="color_class" type="text" value="{{ old('color_class', $emergencyContact->color_class ?? '') }}"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
        </div>
        <div>
            <label for="sort_order" class="mb-2 block text-sm font-semibold text-slate-800">Urutan</label>
            <input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $emergencyContact->sort_order ?? 0) }}"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
        </div>
    </div>
    <label class="flex items-center gap-3 text-sm text-slate-700">
        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-[var(--primary-color)] focus:ring-[var(--primary-color)]/30"
            @checked(old('is_active', $emergencyContact->is_active ?? true))>
        Kontak aktif
    </label>
    <div class="flex justify-end">
        <button type="submit" class="inline-flex rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
            {{ $submitLabel }}
        </button>
    </div>
</div>
