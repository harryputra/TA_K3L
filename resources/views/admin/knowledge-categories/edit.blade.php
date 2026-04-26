@extends('admin.layouts.app')

@section('title', 'Edit Kategori Knowledge')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Knowledge Category</p>
        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Edit Kategori Knowledge</h2>
        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Perbarui nama, slug, atau deskripsi kategori knowledge.</p>

        <form action="{{ route('admin.knowledge-categories.update', $knowledgeCategory) }}" method="POST" class="mt-8">
            @method('PUT')
            @include('admin.knowledge-categories._form', ['submitLabel' => 'Perbarui Kategori'])
        </form>
    </section>
@endsection
