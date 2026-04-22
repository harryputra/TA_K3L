@extends('layouts.app')

@section('title', 'Edit Kategori Insiden')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Kategori Insiden</p>
        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Edit Kategori</h2>
        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Perbarui kategori agar tetap relevan dengan klasifikasi pelaporan yang digunakan.</p>

        <form action="{{ route('admin.incident-categories.update', $incidentCategory) }}" method="POST" class="mt-8">
            @method('PUT')
            @include('admin.incident-categories._form', ['submitLabel' => 'Perbarui Kategori'])
        </form>
    </section>
@endsection
