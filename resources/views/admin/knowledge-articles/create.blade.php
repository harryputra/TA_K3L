@extends('admin.layouts.app')

@section('title', 'Tambah Materi Knowledge')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Admin Knowledge</p>
        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Tambah Materi Knowledge</h2>
        <form action="{{ route('admin.knowledge-articles.store') }}" method="POST" enctype="multipart/form-data" class="mt-8">
            @include('admin.knowledge-articles._form', ['submitLabel' => 'Simpan Materi'])
        </form>
    </section>
@endsection
