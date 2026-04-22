@extends('layouts.app')

@section('title', 'Tambah Kategori Insiden')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Kategori Insiden</p>
        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Tambah Kategori Baru</h2>
        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Tambahkan kategori agar pelaporan insiden bisa diklasifikasikan dengan lebih presisi.</p>

        <form action="{{ route('admin.incident-categories.store') }}" method="POST" class="mt-8">
            @include('admin.incident-categories._form', ['submitLabel' => 'Simpan Kategori'])
        </form>
    </section>
@endsection
