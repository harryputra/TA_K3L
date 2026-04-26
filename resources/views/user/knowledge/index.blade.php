@extends('user.layouts.app')

@section('title', 'Materi K3')

@section('page')
    @php
        $heroArticle = $featuredArticles->first() ?? $latestArticles->first();
        $catalogArticles = $latestArticles->take(6);
        $categoryPills = $categories->take(6);
        $hasFilter = filled($selectedQuery) || filled($selectedCategory);
    @endphp
    <header id="header" class="w-full h-155 flex flex-col items-center justify-center gap-4 relative px-6 -mt-26">
            <div class="mx-auto flex w-full max-w-[1600px] flex-col items-center gap-7">
                <div class="relative z-10 flex flex-col items-center gap-4 text-white">
                    <h1 class="text-center text-5xl font-bold lg:text-7xl">Pusat Materi K3</h1>
                    <p class="max-w-4xl text-center text-lg text-white/90 lg:text-xl">
                        Pelajari modul K3, SOP, dan panduan keselamatan yang relevan untuk aktivitas kampus dan workshop.
                    </p>
                </div>

                <form action="{{ route('user.knowledge.index') }}" data-auto-submit-form data-live-submit
                    data-live-target="[data-live-region='user-knowledge-content']" data-auto-submit-delay="300"
                    class="relative z-10 mt-3 flex w-full max-w-5xl items-center gap-3 rounded-full bg-white p-3 shadow-[0_20px_45px_rgba(0,72,167,0.22)]">
                    @if (filled($selectedCategory))
                        <input type="hidden" name="category" value="{{ $selectedCategory }}">
                    @endif
                    <span class="material-symbols-outlined ml-3 text-4xl text-slate-800">search</span>
                    <input type="text" name="q" value="{{ $selectedQuery }}" placeholder="Cari modul K3, SOP, atau panduan"
                        class="h-14 flex-1 border-0 bg-transparent text-lg font-medium text-slate-700 outline-none placeholder:text-slate-400">
                    <button type="submit"
                        class="inline-flex h-14 min-w-38 items-center justify-center rounded-full bg-[var(--primary-color)] px-8 text-xl font-bold text-white">
                        Cari
                    </button>
                </form>
            </div>
    </header>
    <main class="w-full bg-[#f6f8fc] pb-16 pt-30">
        <section data-live-region="user-knowledge-content" class="mx-auto -mt-2 flex w-full max-w-[1600px] flex-col gap-8 px-4 lg:mt-0 lg:px-8">
            @if ($heroArticle)
                <article
                    class="grid gap-0 overflow-hidden rounded-[1.5rem] bg-white shadow-[0_20px_55px_rgba(15,23,42,0.10)] ring-1 ring-slate-200 lg:grid-cols-[430px_minmax(0,1fr)]">
                    <div class="min-h-[290px] bg-slate-200">
                        <img src="{{ $heroArticle->thumbnail_path ? asset('storage/' . $heroArticle->thumbnail_path) : asset('img/background.jpeg') }}" alt="Materi unggulan" class="h-full w-full object-cover">
                    </div>
                    <div class="flex flex-col gap-5 px-7 py-7 lg:px-8 lg:py-8">
                        <span class="inline-flex w-fit rounded-full bg-[var(--orange)] px-4 py-2 text-sm font-bold text-white">
                            {{ $hasFilter ? 'Hasil Teratas' : 'Sedang Dipelajari' }}
                        </span>
                        <div class="space-y-3">
                            <h2 class="text-4xl font-bold leading-tight text-[var(--primary-color)]">
                                Module : {{ $heroArticle->title }}
                            </h2>
                            <p class="max-w-4xl text-xl font-semibold leading-10 text-slate-800">
                                {{ $heroArticle->summary ?? 'Materi ini belum memiliki ringkasan.' }}
                            </p>
                        </div>
                        <div class="mt-2 grid gap-4 lg:grid-cols-[1fr_220px] lg:items-end">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm font-semibold text-slate-700">
                                    <span>{{ $hasFilter ? 'Hasil Pencarian' : 'Materi Tersedia' }}</span>
                                    <span>{{ $hasFilter ? $latestArticles->count() . ' materi cocok' : $latestArticles->count() . ' materi dipublikasikan' }}</span>
                                </div>
                                <div class="h-5 overflow-hidden rounded-full bg-slate-200">
                                    <div
                                        class="h-full {{ $hasFilter ? 'w-[68%]' : 'w-full' }} rounded-full bg-gradient-to-r from-[#00520d] via-[#008819] to-[#06b6d4]">
                                    </div>
                                </div>
                            </div>
                            @php
                                $heroSlug =
                                    $heroArticle->slug ??
                                    \Illuminate\Support\Str::slug($heroArticle->title ?? 'keselamatan-mesin-bubut-cnc');
                            @endphp
                            <a href="{{ route('user.knowledge.show', $heroSlug) }}"
                                class="inline-flex h-14 items-center justify-center gap-3 rounded-full bg-[var(--primary-color)] px-8 text-lg font-bold text-white">
                                Lanjutkan Belajar
                                <span class="material-symbols-outlined text-2xl">arrow_right_alt</span>
                            </a>
                        </div>
                    </div>
                </article>
            @endif

            <div class="flex flex-wrap gap-4 pt-2">
                <a href="{{ route('user.knowledge.index', array_filter(['q' => $selectedQuery])) }}"
                    class="inline-flex min-h-15 items-center justify-center rounded-full px-10 text-lg font-bold {{ blank($selectedCategory) ? 'bg-[var(--primary-color)] text-white' : 'bg-[#e8edf4] text-[var(--primary-color)]' }}">
                    Semua
                </a>
                @foreach ($categoryPills as $category)
                    <a href="{{ route('user.knowledge.index', array_filter(['q' => $selectedQuery, 'category' => $category->name])) }}"
                        class="inline-flex min-h-15 items-center justify-center rounded-full px-9 text-lg font-bold {{ $selectedCategory === $category->name ? 'bg-[var(--primary-color)] text-white' : 'bg-[#e8edf4] text-[var(--primary-color)]' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            <section
                class="flex flex-col gap-6 rounded-[1.4rem] bg-white px-6 py-7 shadow-[0_12px_35px_rgba(15,23,42,0.06)] ring-1 ring-slate-200 lg:px-7">
                <div class="flex items-end justify-between gap-4 border-b border-slate-200 pb-4">
                    <div>
                        <h2 class="text-5xl font-bold tracking-tight text-black">KATALOG MATERI K3</h2>
                        @if ($hasFilter)
                            <p class="mt-2 text-sm font-medium text-slate-500">
                                Menampilkan hasil
                                @if (filled($selectedQuery))
                                    untuk kata kunci <span class="font-semibold text-slate-800">"{{ $selectedQuery }}"</span>
                                @endif
                                @if (filled($selectedCategory))
                                    pada kategori <span class="font-semibold text-slate-800">{{ $selectedCategory }}</span>
                                @endif
                            </p>
                        @endif
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-slate-800">{{ $latestArticles->count() }} Module Tersedia</span>
                        @if ($hasFilter)
                            <div class="mt-2">
                                <a href="{{ route('user.knowledge.index') }}" class="text-sm font-semibold text-[var(--primary-color)]">
                                    Reset Filter
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid gap-8 xl:grid-cols-3">
                    @forelse ($catalogArticles as $article)
                        @php
                            $articleSlug = $article->slug ?? \Illuminate\Support\Str::slug($article->title);
                        @endphp
                        <article
                            class="overflow-hidden rounded-[1.25rem] bg-white shadow-[0_16px_40px_rgba(15,23,42,0.12)] ring-1 ring-slate-200">
                            <div class="h-72 bg-slate-200">
                                <img src="{{ $article->thumbnail_path ? asset('storage/' . $article->thumbnail_path) : asset('img/background.jpeg') }}" alt="{{ $article->title }}"
                                    class="h-full w-full object-cover">
                            </div>
                            <div class="space-y-5 px-5 py-5">
                                <div class="space-y-3">
                                    <h3 class="text-2xl font-bold leading-tight text-[var(--primary-color)]">
                                        {{ $article->title }}</h3>
                                    <p class="text-lg font-semibold leading-8 text-black">
                                        {{ $article->summary ?? 'Materi ini belum memiliki ringkasan.' }}
                                    </p>
                                </div>

                                <div class="flex items-center justify-between pt-2">
                                    <div class="flex items-center gap-2 text-xl font-semibold text-black">
                                        <span class="material-symbols-outlined text-[var(--primary-color)]">schedule</span>
                                        <span>{{ $article->reading_time ?? '1 Jam' }}</span>
                                    </div>
                                    <a href="{{ route('user.knowledge.show', $articleSlug) }}"
                                        class="inline-flex items-center gap-2 text-xl font-bold text-[var(--primary-color)]">
                                        Mulai Modul
                                        <span class="material-symbols-outlined text-xl">play_circle</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div
                            class="col-span-full rounded-[1.25rem] border border-dashed border-slate-300 px-6 py-16 text-center text-lg font-medium text-slate-500">
                            Belum ada materi K3 yang tersedia.
                        </div>
                    @endforelse
                </div>
            </section>
        </section>
    </main>
@endsection
