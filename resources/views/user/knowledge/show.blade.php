@extends('user.layouts.app')

@section('title', $article->title)

@section('page')
    @php
        $sections = $article->structuredSections();
        $heroImage = $article->thumbnail_path ? asset('storage/' . $article->thumbnail_path) : asset('img/background.jpeg');

        $formatInline = function (string $text): string {
            $escaped = e($text);

            return preg_replace([
                '/&lt;u&gt;(.*?)&lt;\/u&gt;/',
                '/\[(.*?)\]\((https?:\/\/.*?)\)/',
                '/\*\*(.*?)\*\*/',
                '/\*(.*?)\*/',
            ], [
                '<u>$1</u>',
                '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>',
                '<strong>$1</strong>',
                '<em>$1</em>',
            ], $escaped);
        };

        $renderSectionBody = function (string $body, string $listStyle) use ($formatInline): \Illuminate\Support\HtmlString {
            $blocks = preg_split("/\n\s*\n/", trim($body)) ?: [];
            $html = collect($blocks)
                ->filter(fn ($block) => trim($block) !== '')
                ->map(function ($block) use ($listStyle, $formatInline) {
                    $lines = collect(preg_split("/\n/", trim($block)) ?: [])
                        ->map(fn ($line) => trim($line))
                        ->filter()
                        ->values();

                    if ($listStyle !== 'paragraph' && $lines->count() >= 1) {
                        if ($listStyle === 'number') {
                            return '<ol class="space-y-3 pl-6 list-decimal marker:font-semibold marker:text-[var(--primary-color)]">' .
                                $lines->map(fn ($line) => '<li class="pl-1">' . $formatInline($line) . '</li>')->implode('') .
                                '</ol>';
                        }

                        if ($listStyle === 'dash') {
                            return '<ul class="space-y-3">' .
                                $lines->map(fn ($line) => '<li class="flex gap-3"><span class="font-bold text-[var(--primary-color)]">-</span><span>' . $formatInline($line) . '</span></li>')->implode('') .
                                '</ul>';
                        }

                        return '<ul class="space-y-3 pl-6 list-disc marker:text-[var(--primary-color)]">' .
                            $lines->map(fn ($line) => '<li class="pl-1">' . $formatInline($line) . '</li>')->implode('') .
                            '</ul>';
                    }

                    return $lines->map(fn ($line) => '<p class="text-lg leading-9 text-slate-600">' . $formatInline($line) . '</p>')->implode('');
                })
                ->implode('');

            return new \Illuminate\Support\HtmlString($html !== '' ? $html : '<p class="text-lg leading-9 text-slate-500">Bagian ini belum memiliki isi.</p>');
        };

        $embedVideoUrl = function (?string $url): ?string {
            if (! filled($url)) {
                return null;
            }

            if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([^&?/]+)~', $url, $matches)) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }

            if (preg_match('~vimeo\.com/(\d+)~', $url, $matches)) {
                return 'https://player.vimeo.com/video/' . $matches[1];
            }

            return $url;
        };
    @endphp

    <main class="w-full bg-[#f6f8fc] pb-20 pt-30">
        <section class="mx-auto flex w-full max-w-[1880px] flex-col gap-8 px-4 lg:px-8">
            <section class="overflow-hidden rounded-[2rem] bg-white shadow-[0_25px_70px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
                <div class="grid gap-0 2xl:grid-cols-[minmax(0,1.08fr)_420px]">
                    <div class="relative min-h-[34rem] overflow-hidden">
                        <img src="{{ $heroImage }}" alt="{{ $article->title }}" class="absolute inset-0 h-full w-full object-cover">
                        <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(7,45,112,0.88),rgba(10,77,179,0.68),rgba(15,23,42,0.48))]"></div>
                        <div class="relative z-10 flex h-full flex-col justify-end px-7 py-8 lg:px-10 lg:py-10">
                            <span class="inline-flex w-fit rounded-full border border-white/20 bg-white/12 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-white/90">
                                {{ $article->category?->name ?? 'Materi K3' }}
                            </span>
                            <h1 class="mt-6 max-w-5xl text-4xl font-bold leading-tight text-white lg:text-6xl">
                                {{ $article->title }}
                            </h1>
                            <p class="mt-5 max-w-4xl text-lg leading-8 text-white/88 lg:text-2xl">
                                {{ $article->summary ?: 'Materi ini disusun untuk membantu Anda memahami prosedur, risiko, dan tindakan aman di lingkungan kerja kampus.' }}
                            </p>

                            <div class="mt-8 flex flex-wrap gap-3">
                                <span class="inline-flex items-center rounded-full bg-white/14 px-4 py-2 text-sm font-semibold text-white backdrop-blur">
                                    {{ $article->reading_time ?? 'Waktu baca belum diatur' }}
                                </span>
                                <span class="inline-flex items-center rounded-full bg-white/14 px-4 py-2 text-sm font-semibold text-white backdrop-blur">
                                    {{ $sections ? count($sections) . ' section' : '1 section' }}
                                </span>
                                <span class="inline-flex items-center rounded-full bg-white/14 px-4 py-2 text-sm font-semibold text-white backdrop-blur">
                                    {{ optional($article->published_at)->format('d M Y') ?? 'Belum dipublikasikan' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <aside class="flex flex-col justify-between gap-6 bg-[#f8fbff] px-6 py-7 lg:px-7 lg:py-8">
                        <div class="space-y-5">
                            <div class="rounded-[1.4rem] bg-white px-5 py-5 shadow-sm ring-1 ring-slate-200">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Navigasi Materi</p>
                                <div class="mt-4 space-y-3">
                                    @foreach ($sections as $index => $section)
                                        <a href="#section-{{ $index + 1 }}"
                                            class="block rounded-[1rem] border border-slate-200 bg-[#f8fbff] px-4 py-4 transition hover:border-[var(--primary-color)]/20 hover:bg-white">
                                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Section {{ $index + 1 }}</p>
                                            <p class="mt-2 text-sm font-bold text-[var(--primary-color)]">{{ $section['title'] }}</p>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <div class="rounded-[1.4rem] bg-white px-5 py-5 shadow-sm ring-1 ring-slate-200">
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Informasi Cepat</p>
                                <div class="mt-4 space-y-3 text-sm text-slate-600">
                                    <div class="flex items-center justify-between gap-4">
                                        <span>Status</span>
                                        <span class="font-bold text-[var(--primary-color)]">{{ ucfirst($article->status) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-4">
                                        <span>Waktu baca</span>
                                        <span class="font-bold text-[var(--primary-color)]">{{ $article->reading_time ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-4">
                                        <span>Lampiran</span>
                                        <span class="font-bold text-[var(--primary-color)]">{{ $article->attachments->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <a href="{{ route('user.knowledge.index') }}"
                                class="flex items-center justify-between rounded-[1rem] bg-white px-5 py-4 text-sm font-semibold text-slate-700 ring-1 ring-slate-200 transition hover:bg-[#f4f8ff]">
                                <span>Kembali ke katalog</span>
                                <span class="material-symbols-outlined text-[var(--primary-color)]">arrow_back</span>
                            </a>
                            <a href="{{ route('user.incidents.create') }}"
                                class="flex items-center justify-between rounded-[1rem] bg-[var(--primary-color)] px-5 py-4 text-sm font-semibold text-white transition hover:opacity-90">
                                <span>Buat laporan insiden</span>
                                <span class="material-symbols-outlined">arrow_forward</span>
                            </a>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="grid gap-8 2xl:grid-cols-[minmax(0,1fr)_360px]">
                <div class="space-y-8">
                    @foreach ($sections as $index => $section)
                        @php
                            $mediaEmbed = $section['media_type'] === 'video' ? $embedVideoUrl($section['media_url'] ?? null) : null;
                        @endphp
                        <article id="section-{{ $index + 1 }}"
                            class="overflow-hidden rounded-[1.85rem] bg-white shadow-[0_22px_60px_rgba(15,23,42,0.11)] ring-1 ring-slate-200">
                            <div class="border-b border-slate-200 bg-[linear-gradient(180deg,#ffffff,#f8fbff)] px-7 py-6 lg:px-10 lg:py-8">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Section {{ $index + 1 }}</p>
                                        <h2 class="mt-3 text-3xl font-bold text-[var(--primary-color)] lg:text-4xl">
                                            {{ $section['title'] }}
                                        </h2>
                                    </div>
                                    @if (filled($section['caption'] ?? null))
                                        <p class="max-w-xl text-sm leading-7 text-slate-500">
                                            {{ $section['caption'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="grid gap-8 px-7 py-7 lg:px-10 lg:py-10 {{ $section['media_type'] !== 'none' ? 'xl:grid-cols-[minmax(0,1fr)_420px]' : '' }}">
                                <div class="space-y-6">
                                    <div class="prose prose-slate max-w-none prose-p:my-0 prose-p:leading-8 prose-strong:text-slate-900 prose-a:text-[var(--primary-color)] prose-a:no-underline hover:prose-a:underline">
                                        {!! $renderSectionBody($section['body'], $section['list_style']) !!}
                                    </div>
                                </div>

                                @if (($section['media_type'] ?? 'none') === 'image' && filled($section['media_path'] ?? null))
                                    <figure class="overflow-hidden rounded-[1.4rem] border border-slate-200 bg-[#f8fbff]">
                                        <img src="{{ asset('storage/' . $section['media_path']) }}" alt="{{ $section['title'] }}"
                                            class="h-full min-h-[18rem] w-full object-cover">
                                        @if (filled($section['caption'] ?? null))
                                            <figcaption class="px-5 py-4 text-sm leading-7 text-slate-500">
                                                {{ $section['caption'] }}
                                            </figcaption>
                                        @endif
                                    </figure>
                                @elseif (($section['media_type'] ?? 'none') === 'video' && filled($mediaEmbed))
                                    <div class="overflow-hidden rounded-[1.4rem] border border-slate-200 bg-[#f8fbff]">
                                        <div class="aspect-video">
                                            <iframe src="{{ $mediaEmbed }}" title="{{ $section['title'] }}"
                                                class="h-full w-full" allowfullscreen></iframe>
                                        </div>
                                        @if (filled($section['caption'] ?? null))
                                            <div class="px-5 py-4 text-sm leading-7 text-slate-500">
                                                {{ $section['caption'] }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach

                    @if (filled($article->video_url))
                        @php($mainVideoEmbed = $embedVideoUrl($article->video_url))
                        @if ($mainVideoEmbed)
                            <section class="overflow-hidden rounded-[1.85rem] bg-white shadow-[0_22px_60px_rgba(15,23,42,0.11)] ring-1 ring-slate-200">
                                <div class="border-b border-slate-200 px-7 py-6 lg:px-10">
                                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Video Utama</p>
                                    <h2 class="mt-3 text-3xl font-bold text-[var(--primary-color)]">Pendukung Visual Materi</h2>
                                </div>
                                <div class="p-7 lg:p-10">
                                    <div class="overflow-hidden rounded-[1.4rem] border border-slate-200 bg-[#f8fbff]">
                                        <div class="aspect-video">
                                            <iframe src="{{ $mainVideoEmbed }}" title="{{ $article->title }}" class="h-full w-full" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        @endif
                    @endif
                </div>

                <aside class="space-y-8">
                    @if ($article->attachments->isNotEmpty())
                        <section class="rounded-[1.6rem] bg-white px-6 py-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Lampiran</p>
                            <h3 class="mt-3 text-2xl font-bold text-[var(--primary-color)]">File Pendukung</h3>
                            <div class="mt-5 space-y-3">
                                @foreach ($article->attachments as $attachment)
                                    <div class="rounded-[1rem] bg-[#f8fbff] px-4 py-4 ring-1 ring-slate-200">
                                        <p class="text-sm font-bold text-[var(--primary-color)]">{{ $attachment->file_name }}</p>
                                        <p class="mt-1 text-xs uppercase tracking-[0.18em] text-slate-400">{{ $attachment->file_type }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section class="rounded-[1.6rem] bg-white px-6 py-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Lanjutkan Belajar</p>
                        <h3 class="mt-3 text-2xl font-bold text-[var(--primary-color)]">Materi Terkait</h3>
                        <div class="mt-5 space-y-4">
                            @foreach ($relatedModules as $relatedModule)
                                <a href="{{ route('user.knowledge.show', $relatedModule->slug) }}"
                                    class="block rounded-[1rem] bg-[#f8fbff] px-4 py-4 ring-1 ring-slate-200 transition hover:bg-white hover:shadow-sm">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $relatedModule->category?->name ?? 'Materi terkait' }}</p>
                                    <h4 class="mt-2 text-lg font-bold text-[var(--primary-color)]">{{ $relatedModule->title }}</h4>
                                    <p class="mt-2 text-sm leading-7 text-slate-500">
                                        {{ $relatedModule->summary ?: 'Lanjutkan pemahaman Anda dengan materi lain yang masih relevan.' }}
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    </section>
                </aside>
            </section>
        </section>
    </main>
@endsection
