@extends('admin.layouts.app')

@section('title', 'Tambah Kategori Knowledge')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Knowledge Category</p>
        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Tambah Kategori Knowledge</h2>
        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Buat kategori baru untuk mengelompokkan materi knowledge center.</p>

        <form action="{{ route('admin.knowledge-categories.store') }}" method="POST" class="mt-8">
            @include('admin.knowledge-categories._form', ['submitLabel' => 'Simpan Kategori'])
        </form>
    </section>
@endsection
