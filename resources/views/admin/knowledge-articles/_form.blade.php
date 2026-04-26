@csrf

@php
    $coverPreview = ($knowledgeArticle->thumbnail_path ?? null)
        ? asset('storage/' . $knowledgeArticle->thumbnail_path)
        : asset('img/background.jpeg');

    $initialSections = old('sections');

    if (! is_array($initialSections)) {
        $initialSections = method_exists($knowledgeArticle, 'structuredSections')
            ? $knowledgeArticle->structuredSections()
            : [];
    }

    if (blank($initialSections)) {
        $initialSections = [[
            'id' => 'section-1',
            'title' => 'Pendahuluan',
            'body' => '',
            'list_style' => 'paragraph',
            'media_type' => 'none',
            'media_path' => null,
            'media_url' => null,
            'caption' => null,
        ]];
    }
@endphp

<div class="grid gap-6">
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_360px]">
        <div class="space-y-5">
            <div>
                <label for="knowledge_category_id" class="mb-2 block text-sm font-semibold text-slate-800">Kategori</label>
                <select id="knowledge_category_id" name="knowledge_category_id"
                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('knowledge_category_id', $knowledgeArticle->knowledge_category_id ?? null) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="title" class="mb-2 block text-sm font-semibold text-slate-800">Judul materi</label>
                <input id="title" name="title" type="text" value="{{ old('title', $knowledgeArticle->title ?? '') }}"
                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
            </div>

            <div class="grid gap-5 md:grid-cols-3">
                <div>
                    <label for="slug" class="mb-2 block text-sm font-semibold text-slate-800">Slug</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $knowledgeArticle->slug ?? '') }}"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                </div>
                <div>
                    <label for="reading_time" class="mb-2 block text-sm font-semibold text-slate-800">Waktu baca</label>
                    <input id="reading_time" name="reading_time" type="text" value="{{ old('reading_time', $knowledgeArticle->reading_time ?? '') }}"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                        placeholder="Contoh: 5 menit">
                </div>
                <div>
                    <label for="status" class="mb-2 block text-sm font-semibold text-slate-800">Status</label>
                    <select id="status" name="status"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $knowledgeArticle->status ?? 'draft') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="summary" class="mb-2 block text-sm font-semibold text-slate-800">Ringkasan</label>
                <textarea id="summary" name="summary" rows="4"
                    class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">{{ old('summary', $knowledgeArticle->summary ?? '') }}</textarea>
            </div>
        </div>

        <aside class="space-y-5 rounded-[1.75rem] border border-slate-200 bg-[#f8fbff] p-5">
            <div>
                <label for="cover_image" class="mb-2 block text-sm font-semibold text-slate-800">Cover materi</label>
                <input id="cover_image" name="cover_image" type="file" accept=".jpg,.jpeg,.png,.webp"
                    class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-[var(--primary-color)] file:px-4 file:py-2 file:font-semibold file:text-white hover:file:opacity-90 focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                <p class="mt-2 text-xs leading-6 text-slate-500">Unggah gambar JPG, PNG, atau WEBP maksimal 5MB untuk dijadikan cover materi.</p>
                @error('cover_image')
                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="overflow-hidden rounded-[1.4rem] border border-slate-200 bg-slate-100 shadow-sm">
                <img id="cover-image-preview" src="{{ $coverPreview }}"
                    alt="Preview cover materi" class="h-52 w-full object-cover">
            </div>

            <div>
                <label for="video_url" class="mb-2 block text-sm font-semibold text-slate-800">Video utama materi</label>
                <input id="video_url" name="video_url" type="url" value="{{ old('video_url', $knowledgeArticle->video_url ?? '') }}"
                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                    placeholder="https://youtube.com/...">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-800">Path cover tersimpan</label>
                <input type="text" value="{{ $knowledgeArticle->thumbnail_path ?? 'Belum ada cover diunggah' }}"
                    class="w-full rounded-2xl border border-slate-300 bg-slate-100 px-4 py-3 text-sm text-slate-500 outline-none" readonly>
            </div>
        </aside>
    </div>

    <div class="rounded-[2rem] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-[var(--primary-color)]">Builder Section</p>
                <h3 class="mt-2 text-2xl font-bold text-slate-900">Susun materi per bagian agar lebih rapi</h3>
                <p class="mt-2 text-sm leading-7 text-slate-500">
                    Setiap section bisa punya judul, isi, gaya list, serta media gambar atau video sendiri.
                </p>
            </div>
            <button type="button" id="add-section-button"
                class="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                Tambah Section
            </button>
        </div>

        <div class="grid gap-0 xl:grid-cols-[minmax(0,1.15fr)_440px]">
            <div class="border-b border-slate-200 xl:border-r xl:border-b-0">
                <div id="sections-builder" class="space-y-5 p-5">
                    @foreach ($initialSections as $index => $section)
                        @php
                            $mediaPath = $section['media_path'] ?? null;
                            $mediaPreview = $mediaPath ? asset('storage/' . $mediaPath) : null;
                        @endphp
                        <article class="section-card rounded-[1.6rem] border border-slate-200 bg-[#fbfdff] p-5 shadow-sm" data-section-card draggable="true">
                            <input type="hidden" name="sections[{{ $index }}][id]" value="{{ $section['id'] ?? 'section-' . ($index + 1) }}" data-section-field="id">

                            <div class="mb-4 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <button type="button"
                                        class="inline-flex h-11 w-11 cursor-grab items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 shadow-sm active:cursor-grabbing"
                                        data-drag-handle
                                        aria-label="Geser section">
                                        <span class="material-symbols-outlined text-xl">drag_indicator</span>
                                    </button>
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Section {{ $index + 1 }}</p>
                                    <div>
                                        <h4 class="text-lg font-bold text-slate-900">Blok Materi</h4>
                                        <p class="text-xs text-slate-400">Drag and drop untuk ubah urutan dengan cepat.</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <button type="button" class="inline-flex items-center justify-center rounded-full border border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-600 transition hover:bg-slate-50" data-move-section="up">
                                        Naik
                                    </button>
                                    <button type="button" class="inline-flex items-center justify-center rounded-full border border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-600 transition hover:bg-slate-50" data-move-section="down">
                                        Turun
                                    </button>
                                    <button type="button" class="inline-flex items-center justify-center rounded-full border border-[var(--primary-color)]/20 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.18em] text-[var(--primary-color)] transition hover:bg-[var(--blue-low-opacity)]/40" data-duplicate-section>
                                        Duplikat
                                    </button>
                                    <button type="button" class="inline-flex items-center justify-center rounded-full border border-rose-200 px-4 py-2 text-[11px] font-bold uppercase tracking-[0.18em] text-rose-600 transition hover:bg-rose-50" data-remove-section>
                                        Hapus
                                    </button>
                                </div>
                            </div>

                            <div class="grid gap-4">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-800">Judul section</label>
                                    <input type="text" name="sections[{{ $index }}][title]" value="{{ $section['title'] ?? '' }}"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                                        data-section-field="title">
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-800">Gaya list</label>
                                        <select name="sections[{{ $index }}][list_style]"
                                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                                            data-section-field="list_style">
                                            <option value="paragraph" @selected(($section['list_style'] ?? 'paragraph') === 'paragraph')>Paragraf biasa</option>
                                            <option value="bullet" @selected(($section['list_style'] ?? '') === 'bullet')>Dot / Bullet</option>
                                            <option value="dash" @selected(($section['list_style'] ?? '') === 'dash')>Dash / -</option>
                                            <option value="number" @selected(($section['list_style'] ?? '') === 'number')>Angka / 1. 2. 3.</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-800">Media section</label>
                                        <select name="sections[{{ $index }}][media_type]"
                                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                                            data-section-field="media_type">
                                            <option value="none" @selected(($section['media_type'] ?? 'none') === 'none')>Tanpa media</option>
                                            <option value="image" @selected(($section['media_type'] ?? '') === 'image')>Gambar</option>
                                            <option value="video" @selected(($section['media_type'] ?? '') === 'video')>Video</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <div class="mb-2 flex flex-wrap gap-2">
                                        <button type="button" data-inline-format="bold" class="inline-flex rounded-full bg-[var(--primary-color)]/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[var(--primary-color)]">Bold</button>
                                        <button type="button" data-inline-format="italic" class="inline-flex rounded-full bg-[var(--primary-color)]/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[var(--primary-color)]">Italic</button>
                                        <button type="button" data-inline-format="underline" class="inline-flex rounded-full bg-[var(--primary-color)]/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[var(--primary-color)]">Underline</button>
                                        <button type="button" data-inline-format="link" class="inline-flex rounded-full bg-[var(--primary-color)]/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[var(--primary-color)]">Link</button>
                                        <button type="button" data-inline-format="quote" class="inline-flex rounded-full bg-[var(--primary-color)]/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[var(--primary-color)]">Quote</button>
                                    </div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-800">Isi section</label>
                                    <textarea name="sections[{{ $index }}][body]" rows="7"
                                        class="w-full rounded-[1.4rem] border border-slate-300 bg-white px-4 py-4 text-sm leading-7 outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                                        placeholder="Tulis paragraf atau daftar item. Satu baris untuk satu item list." data-section-field="body">{{ $section['body'] ?? '' }}</textarea>
                                </div>

                                <div class="media-image-fields grid gap-4 {{ ($section['media_type'] ?? 'none') === 'image' ? '' : 'hidden' }}" data-media-image-fields>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-800">Gambar section</label>
                                        <input type="file" name="sections[{{ $index }}][media_image]" accept=".jpg,.jpeg,.png,.webp"
                                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-[var(--primary-color)] file:px-4 file:py-2 file:font-semibold file:text-white hover:file:opacity-90 focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                                            data-section-image-input>
                                    </div>
                                    <div class="overflow-hidden rounded-[1.2rem] border border-slate-200 bg-slate-100">
                                        <img src="{{ $mediaPreview ?? asset('img/background.jpeg') }}" class="h-48 w-full object-cover" alt="Preview gambar section" data-section-image-preview data-existing-src="{{ $mediaPreview ?? asset('img/background.jpeg') }}">
                                    </div>
                                </div>

                                <div class="media-video-fields {{ ($section['media_type'] ?? 'none') === 'video' ? '' : 'hidden' }} grid gap-4" data-media-video-fields>
                                    <label class="mb-2 block text-sm font-semibold text-slate-800">URL video section</label>
                                    <input type="url" name="sections[{{ $index }}][media_url]" value="{{ $section['media_url'] ?? '' }}"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                                        placeholder="https://youtube.com/watch?v=..." data-section-field="media_url">
                                    <div class="overflow-hidden rounded-[1.2rem] border border-slate-200 bg-slate-100 {{ filled($section['media_url'] ?? null) ? '' : 'hidden' }}" data-section-video-preview-wrap>
                                        <iframe src="" class="aspect-video w-full" allowfullscreen data-section-video-preview></iframe>
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-800">Caption media</label>
                                    <input type="text" name="sections[{{ $index }}][caption]" value="{{ $section['caption'] ?? '' }}"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                                        placeholder="Keterangan singkat untuk gambar atau video" data-section-field="caption">
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <aside class="bg-[#f8fbff] p-5">
                <div class="mb-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-[var(--primary-color)]">Preview Materi</p>
                    <h3 class="mt-2 text-2xl font-bold text-slate-900">Hasil tampilan ke user</h3>
                </div>

                <div id="knowledge-live-preview" class="overflow-hidden rounded-[1.6rem] border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-5 py-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Live Preview</p>
                        <h4 class="mt-2 text-2xl font-bold text-[var(--primary-color)]" data-preview-title>Judul materi</h4>
                        <p class="mt-3 text-sm leading-7 text-slate-500" data-preview-summary>Ringkasan materi akan muncul di sini.</p>
                    </div>
                    <div class="space-y-5 px-5 py-5" data-preview-sections></div>
                </div>
            </aside>
        </div>
    </div>

    <textarea name="content" class="hidden" aria-hidden="true">{{ old('content', $knowledgeArticle->content ?? '') }}</textarea>

    <div class="flex justify-end">
        <button type="submit" class="inline-flex rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
            {{ $submitLabel }}
        </button>
    </div>
</div>

<template id="section-template">
    <article class="section-card rounded-[1.6rem] border border-slate-200 bg-[#fbfdff] p-5 shadow-sm" data-section-card draggable="true">
        <input type="hidden" name="sections[__INDEX__][id]" value="section-__NUMBER__" data-section-field="id">

        <div class="mb-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <button type="button"
                    class="inline-flex h-11 w-11 cursor-grab items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 shadow-sm active:cursor-grabbing"
                    data-drag-handle
                    aria-label="Geser section">
                    <span class="material-symbols-outlined text-xl">drag_indicator</span>
                </button>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Section __NUMBER__</p>
                <div>
                    <h4 class="text-lg font-bold text-slate-900">Blok Materi</h4>
                    <p class="text-xs text-slate-400">Drag and drop untuk ubah urutan dengan cepat.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center justify-end gap-2">
                <button type="button" class="inline-flex items-center justify-center rounded-full border border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-600 transition hover:bg-slate-50" data-move-section="up">
                    Naik
                </button>
                <button type="button" class="inline-flex items-center justify-center rounded-full border border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-600 transition hover:bg-slate-50" data-move-section="down">
                    Turun
                </button>
                <button type="button" class="inline-flex items-center justify-center rounded-full border border-[var(--primary-color)]/20 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.18em] text-[var(--primary-color)] transition hover:bg-[var(--blue-low-opacity)]/40" data-duplicate-section>
                    Duplikat
                </button>
                <button type="button" class="inline-flex items-center justify-center rounded-full border border-rose-200 px-4 py-2 text-[11px] font-bold uppercase tracking-[0.18em] text-rose-600 transition hover:bg-rose-50" data-remove-section>
                    Hapus
                </button>
            </div>
        </div>

        <div class="grid gap-4">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-800">Judul section</label>
                <input type="text" name="sections[__INDEX__][title]"
                    class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                    data-section-field="title">
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Gaya list</label>
                    <select name="sections[__INDEX__][list_style]"
                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                        data-section-field="list_style">
                        <option value="paragraph">Paragraf biasa</option>
                        <option value="bullet">Dot / Bullet</option>
                        <option value="dash">Dash / -</option>
                        <option value="number">Angka / 1. 2. 3.</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Media section</label>
                    <select name="sections[__INDEX__][media_type]"
                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                        data-section-field="media_type">
                        <option value="none">Tanpa media</option>
                        <option value="image">Gambar</option>
                        <option value="video">Video</option>
                    </select>
                </div>
            </div>

            <div>
                <div class="mb-2 flex flex-wrap gap-2">
                    <button type="button" data-inline-format="bold" class="inline-flex rounded-full bg-[var(--primary-color)]/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[var(--primary-color)]">Bold</button>
                    <button type="button" data-inline-format="italic" class="inline-flex rounded-full bg-[var(--primary-color)]/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[var(--primary-color)]">Italic</button>
                    <button type="button" data-inline-format="underline" class="inline-flex rounded-full bg-[var(--primary-color)]/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[var(--primary-color)]">Underline</button>
                    <button type="button" data-inline-format="link" class="inline-flex rounded-full bg-[var(--primary-color)]/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[var(--primary-color)]">Link</button>
                    <button type="button" data-inline-format="quote" class="inline-flex rounded-full bg-[var(--primary-color)]/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[var(--primary-color)]">Quote</button>
                </div>
                <label class="mb-2 block text-sm font-semibold text-slate-800">Isi section</label>
                <textarea name="sections[__INDEX__][body]" rows="7"
                    class="w-full rounded-[1.4rem] border border-slate-300 bg-white px-4 py-4 text-sm leading-7 outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                    placeholder="Tulis paragraf atau daftar item. Satu baris untuk satu item list." data-section-field="body"></textarea>
            </div>

            <div class="media-image-fields hidden grid gap-4" data-media-image-fields>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Gambar section</label>
                    <input type="file" name="sections[__INDEX__][media_image]" accept=".jpg,.jpeg,.png,.webp"
                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-[var(--primary-color)] file:px-4 file:py-2 file:font-semibold file:text-white hover:file:opacity-90 focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                        data-section-image-input>
                </div>
                <div class="overflow-hidden rounded-[1.2rem] border border-slate-200 bg-slate-100">
                    <img src="{{ asset('img/background.jpeg') }}" class="h-48 w-full object-cover" alt="Preview gambar section" data-section-image-preview data-existing-src="{{ asset('img/background.jpeg') }}">
                </div>
            </div>

            <div class="media-video-fields hidden grid gap-4" data-media-video-fields>
                <label class="mb-2 block text-sm font-semibold text-slate-800">URL video section</label>
                <input type="url" name="sections[__INDEX__][media_url]"
                    class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                    placeholder="https://youtube.com/watch?v=..." data-section-field="media_url">
                <div class="hidden overflow-hidden rounded-[1.2rem] border border-slate-200 bg-slate-100" data-section-video-preview-wrap>
                    <iframe src="" class="aspect-video w-full" allowfullscreen data-section-video-preview></iframe>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-800">Caption media</label>
                <input type="text" name="sections[__INDEX__][caption]"
                    class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                    placeholder="Keterangan singkat untuk gambar atau video" data-section-field="caption">
            </div>
        </div>
    </article>
</template>

@push('scripts')
    <script>
        (() => {
            const coverInput = document.getElementById('cover_image');
            const coverPreview = document.getElementById('cover-image-preview');
            const coverFallbackSrc = @json($coverPreview);
            const sectionsBuilder = document.getElementById('sections-builder');
            const addSectionButton = document.getElementById('add-section-button');
            const sectionTemplate = document.getElementById('section-template');
            const previewTitle = document.querySelector('[data-preview-title]');
            const previewSummary = document.querySelector('[data-preview-summary]');
            const previewSections = document.querySelector('[data-preview-sections]');
            const titleInput = document.getElementById('title');
            const summaryInput = document.getElementById('summary');
            let draggedCard = null;
            const createEmbedUrl = (url) => {
                if (!url) {
                    return '';
                }

                const value = url.trim();
                const youtubeMatch = value.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&?/]+)/i);
                if (youtubeMatch) {
                    return `https://www.youtube.com/embed/${youtubeMatch[1]}`;
                }

                const vimeoMatch = value.match(/vimeo\.com\/(\d+)/i);
                if (vimeoMatch) {
                    return `https://player.vimeo.com/video/${vimeoMatch[1]}`;
                }

                return value;
            };

            if (coverInput && coverPreview) {
                coverInput.addEventListener('change', (event) => {
                    const [file] = event.target.files || [];
                    coverPreview.src = file ? URL.createObjectURL(file) : coverFallbackSrc;
                });
            }

            const escapeHtml = (value) => value
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            const applyInlineFormat = (text) => escapeHtml(text)
                .replace(/&lt;u&gt;(.*?)&lt;\/u&gt;/g, '<u>$1</u>')
                .replace(/\[(.*?)\]\((https?:\/\/.*?)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>')
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>');

            const insertAroundSelection = (textarea, before, after = '') => {
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const selected = textarea.value.slice(start, end);
                const replacement = `${before}${selected || 'Tulis di sini'}${after}`;
                textarea.setRangeText(replacement, start, end, 'end');
                textarea.focus();
                renderArticlePreview();
            };

            const insertBlockPrefix = (textarea, prefix) => {
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const selected = textarea.value.slice(start, end) || 'Tulis kutipan di sini';
                const lines = selected.split('\n').map((line) => `${prefix}${line}`);
                textarea.setRangeText(lines.join('\n'), start, end, 'end');
                textarea.focus();
                renderArticlePreview();
            };

            const getCards = () => Array.from(sectionsBuilder.querySelectorAll('[data-section-card]'));

            const syncSectionIndexes = () => {
                getCards().forEach((card, index) => {
                    card.querySelectorAll('input, textarea, select').forEach((field) => {
                        if (!field.name) {
                            return;
                        }

                        field.name = field.name.replace(/sections\[\d+\]/, `sections[${index}]`);
                    });

                    const idField = card.querySelector('[data-section-field="id"]');
                    if (idField && !idField.value) {
                        idField.value = `section-${index + 1}`;
                    }

                    const label = card.querySelector('p.text-xs');
                    if (label) {
                        label.textContent = `Section ${index + 1}`;
                    }
                });
            };

            const toggleMediaFields = (card) => {
                const mediaType = card.querySelector('[data-section-field="media_type"]')?.value || 'none';
                card.querySelector('[data-media-image-fields]')?.classList.toggle('hidden', mediaType !== 'image');
                card.querySelector('[data-media-video-fields]')?.classList.toggle('hidden', mediaType !== 'video');
            };

            const syncVideoPreview = (card) => {
                const wrap = card.querySelector('[data-section-video-preview-wrap]');
                const frame = card.querySelector('[data-section-video-preview]');
                const input = card.querySelector('[data-section-field="media_url"]');
                const mediaType = card.querySelector('[data-section-field="media_type"]')?.value || 'none';
                const embedUrl = createEmbedUrl(input?.value || '');

                if (!wrap || !frame) {
                    return;
                }

                if (mediaType === 'video' && embedUrl) {
                    frame.src = embedUrl;
                    wrap.classList.remove('hidden');
                    return;
                }

                frame.src = '';
                wrap.classList.add('hidden');
            };

            const attachImagePreview = (card) => {
                const input = card.querySelector('[data-section-image-input]');
                const preview = card.querySelector('[data-section-image-preview]');

                if (!input || !preview || input.dataset.bound === 'true') {
                    return;
                }

                input.dataset.bound = 'true';
                input.addEventListener('change', (event) => {
                    const [file] = event.target.files || [];
                    preview.src = file ? URL.createObjectURL(file) : preview.dataset.existingSrc;
                    renderArticlePreview();
                });
            };

            const bindCard = (card) => {
                toggleMediaFields(card);
                attachImagePreview(card);
                syncVideoPreview(card);

                card.querySelectorAll('input, textarea, select').forEach((field) => {
                    field.addEventListener('input', renderArticlePreview);
                    field.addEventListener('change', () => {
                        toggleMediaFields(card);
                        syncVideoPreview(card);
                        renderArticlePreview();
                    });
                });

                card.querySelectorAll('[data-inline-format]').forEach((button) => {
                    if (button.dataset.bound === 'true') {
                        return;
                    }

                    button.dataset.bound = 'true';
                    button.addEventListener('click', () => {
                        const textarea = card.querySelector('[data-section-field="body"]');

                        switch (button.dataset.inlineFormat) {
                            case 'bold':
                                insertAroundSelection(textarea, '**', '**');
                                break;
                            case 'italic':
                                insertAroundSelection(textarea, '*', '*');
                                break;
                            case 'underline':
                                insertAroundSelection(textarea, '<u>', '</u>');
                                break;
                            case 'link':
                                insertAroundSelection(textarea, '[Teks Link](', 'https://example.com)');
                                break;
                            case 'quote':
                                insertBlockPrefix(textarea, '> ');
                                break;
                        }
                    });
                });

                const removeButton = card.querySelector('[data-remove-section]');
                if (removeButton && removeButton.dataset.bound !== 'true') {
                    removeButton.dataset.bound = 'true';
                    removeButton.addEventListener('click', () => {
                        if (getCards().length === 1) {
                            return;
                        }

                        card.remove();
                        syncSectionIndexes();
                        renderArticlePreview();
                    });
                }

                const dragHandle = card.querySelector('[data-drag-handle]');
                if (dragHandle && dragHandle.dataset.bound !== 'true') {
                    dragHandle.dataset.bound = 'true';
                    dragHandle.addEventListener('mousedown', () => {
                        card.dataset.dragIntent = 'true';
                    });
                    dragHandle.addEventListener('mouseup', () => {
                        delete card.dataset.dragIntent;
                    });
                }

                if (card.dataset.dragBound !== 'true') {
                    card.dataset.dragBound = 'true';

                    card.addEventListener('dragstart', (event) => {
                        if (card.dataset.dragIntent !== 'true') {
                            event.preventDefault();
                            return;
                        }

                        draggedCard = card;
                        card.classList.add('opacity-60', 'ring-2', 'ring-[var(--primary-color)]/30');
                        event.dataTransfer.effectAllowed = 'move';
                    });

                    card.addEventListener('dragend', () => {
                        draggedCard = null;
                        delete card.dataset.dragIntent;
                        card.classList.remove('opacity-60', 'ring-2', 'ring-[var(--primary-color)]/30');
                        getCards().forEach((item) => item.classList.remove('border-[var(--primary-color)]', 'bg-[#f4f8ff]'));
                    });

                    card.addEventListener('dragover', (event) => {
                        if (!draggedCard || draggedCard === card) {
                            return;
                        }

                        event.preventDefault();
                        card.classList.add('border-[var(--primary-color)]', 'bg-[#f4f8ff]');
                    });

                    card.addEventListener('dragleave', () => {
                        if (draggedCard !== card) {
                            card.classList.remove('border-[var(--primary-color)]', 'bg-[#f4f8ff]');
                        }
                    });

                    card.addEventListener('drop', (event) => {
                        if (!draggedCard || draggedCard === card) {
                            return;
                        }

                        event.preventDefault();
                        card.classList.remove('border-[var(--primary-color)]', 'bg-[#f4f8ff]');

                        const cardRect = card.getBoundingClientRect();
                        const shouldInsertAfter = event.clientY > cardRect.top + (cardRect.height / 2);

                        if (shouldInsertAfter) {
                            card.insertAdjacentElement('afterend', draggedCard);
                        } else {
                            card.insertAdjacentElement('beforebegin', draggedCard);
                        }

                        syncSectionIndexes();
                        renderArticlePreview();
                    });
                }

                const duplicateButton = card.querySelector('[data-duplicate-section]');
                if (duplicateButton && duplicateButton.dataset.bound !== 'true') {
                    duplicateButton.dataset.bound = 'true';
                    duplicateButton.addEventListener('click', () => {
                        const index = getCards().length;
                        const html = sectionTemplate.innerHTML
                            .replaceAll('__INDEX__', index)
                            .replaceAll('__NUMBER__', index + 1);

                        card.insertAdjacentHTML('afterend', html);
                        const clonedCard = card.nextElementSibling;

                        clonedCard.querySelectorAll('input, textarea, select').forEach((field) => {
                            const sourceName = field.name.replace(/sections\[\d+\]/, '');
                            const sourceField = card.querySelector(`[name^="sections["][name$="${sourceName}"]`);

                            if (!sourceField) {
                                return;
                            }

                            if (field.type === 'file') {
                                return;
                            }

                            field.value = sourceField.value;
                        });

                        const clonedId = clonedCard.querySelector('[data-section-field="id"]');
                        if (clonedId) {
                            clonedId.value = `section-${Date.now()}`;
                        }

                        const clonedImage = clonedCard.querySelector('[data-section-image-preview]');
                        const sourceImage = card.querySelector('[data-section-image-preview]');
                        if (clonedImage && sourceImage) {
                            clonedImage.src = sourceImage.src;
                            clonedImage.dataset.existingSrc = sourceImage.src;
                        }

                        bindCard(clonedCard);
                        syncSectionIndexes();
                        syncVideoPreview(clonedCard);
                        renderArticlePreview();
                    });
                }

                card.querySelectorAll('[data-move-section]').forEach((button) => {
                    if (button.dataset.bound === 'true') {
                        return;
                    }

                    button.dataset.bound = 'true';
                    button.addEventListener('click', () => {
                        const direction = button.dataset.moveSection;
                        const sibling = direction === 'up' ? card.previousElementSibling : card.nextElementSibling;

                        if (!sibling) {
                            return;
                        }

                        if (direction === 'up') {
                            sectionsBuilder.insertBefore(card, sibling);
                        } else {
                            sectionsBuilder.insertBefore(sibling, card);
                        }

                        syncSectionIndexes();
                        renderArticlePreview();
                    });
                });
            };

            const renderBody = (body, listStyle) => {
                const blocks = body.split(/\n\s*\n/).filter(Boolean);

                if (blocks.length === 0) {
                    return '<p class="text-slate-400">Isi section masih kosong.</p>';
                }

                return blocks.map((block) => {
                    const lines = block.split('\n').map((line) => line.trim()).filter(Boolean);

                    if (listStyle !== 'paragraph' && lines.length >= 1) {
                        if (listStyle === 'number') {
                            return `<ol class="space-y-3 pl-6 list-decimal">${lines.map((line) => `<li>${applyInlineFormat(line)}</li>`).join('')}</ol>`;
                        }

                        if (listStyle === 'dash') {
                            return `<ul class="space-y-3">${lines.map((line) => `<li class="flex gap-3"><span class="font-bold text-[var(--primary-color)]">-</span><span>${applyInlineFormat(line)}</span></li>`).join('')}</ul>`;
                        }

                        return `<ul class="space-y-3 list-disc pl-6 marker:text-[var(--primary-color)]">${lines.map((line) => `<li>${applyInlineFormat(line)}</li>`).join('')}</ul>`;
                    }

                    return lines.map((line) => `<p class="leading-8 text-slate-600">${applyInlineFormat(line)}</p>`).join('');
                }).join('');
            };

            const renderArticlePreview = () => {
                if (previewTitle) {
                    previewTitle.textContent = titleInput?.value?.trim() || 'Judul materi';
                }

                if (previewSummary) {
                    previewSummary.textContent = summaryInput?.value?.trim() || 'Ringkasan materi akan muncul di sini.';
                }

                if (!previewSections) {
                    return;
                }

                const html = getCards().map((card, index) => {
                    const title = card.querySelector('[data-section-field="title"]')?.value?.trim() || `Section ${index + 1}`;
                    const body = card.querySelector('[data-section-field="body"]')?.value?.trim() || '';
                    const listStyle = card.querySelector('[data-section-field="list_style"]')?.value || 'bullet';
                    const mediaType = card.querySelector('[data-section-field="media_type"]')?.value || 'none';
                    const mediaUrl = card.querySelector('[data-section-field="media_url"]')?.value?.trim() || '';
                    const caption = card.querySelector('[data-section-field="caption"]')?.value?.trim() || '';
                    const imagePreview = card.querySelector('[data-section-image-preview]')?.src || '';
                    const embedUrl = createEmbedUrl(mediaUrl);

                    let mediaHtml = '';

                    if (mediaType === 'image' && imagePreview) {
                        mediaHtml = `
                            <figure class="overflow-hidden rounded-[1.1rem] border border-slate-200 bg-slate-100">
                                <img src="${imagePreview}" alt="${title}" class="h-44 w-full object-cover">
                                ${caption ? `<figcaption class="px-4 py-3 text-xs font-medium text-slate-500">${escapeHtml(caption)}</figcaption>` : ''}
                            </figure>
                        `;
                    }

                    if (mediaType === 'video' && embedUrl) {
                        mediaHtml = `
                            <div class="overflow-hidden rounded-[1.1rem] border border-slate-200 bg-slate-50">
                                <iframe src="${embedUrl}" class="aspect-video w-full" allowfullscreen></iframe>
                                ${caption ? `<p class="px-4 py-3 text-xs text-slate-500">${escapeHtml(caption)}</p>` : ''}
                            </div>
                        `;
                    }

                    return `
                        <section class="rounded-[1.3rem] border border-slate-200 bg-[#fbfdff] p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Section ${index + 1}</p>
                            <h5 class="mt-2 text-xl font-bold text-[var(--primary-color)]">${escapeHtml(title)}</h5>
                            <div class="mt-3 space-y-4 text-sm text-slate-600">
                                ${renderBody(body, listStyle)}
                                ${mediaHtml}
                            </div>
                        </section>
                    `;
                }).join('');

                previewSections.innerHTML = html || '<p class="text-sm text-slate-400">Section materi akan muncul di sini.</p>';
            };

            addSectionButton?.addEventListener('click', () => {
                const index = getCards().length;
                const html = sectionTemplate.innerHTML
                    .replaceAll('__INDEX__', index)
                    .replaceAll('__NUMBER__', index + 1);

                sectionsBuilder.insertAdjacentHTML('beforeend', html);
                const newCard = getCards().at(-1);
                bindCard(newCard);
                syncSectionIndexes();
                renderArticlePreview();
            });

            getCards().forEach(bindCard);
            titleInput?.addEventListener('input', renderArticlePreview);
            summaryInput?.addEventListener('input', renderArticlePreview);
            syncSectionIndexes();
            renderArticlePreview();
        })();
    </script>
@endpush
