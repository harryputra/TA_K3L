@extends('layouts.app')

@section('title', 'Tambah Lokasi')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Master Lokasi</p>
        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Tambah Lokasi Baru</h2>
        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Tambahkan lokasi yang akan tersedia di modul pelaporan dan inspeksi.</p>

        <form action="{{ route('admin.locations.store') }}" method="POST" class="mt-8">
            @include('admin.locations._form', ['submitLabel' => 'Simpan Lokasi'])
        </form>
    </section>
@endsection
