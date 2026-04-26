@extends('admin.layouts.app')

@section('title', 'Edit Lokasi')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Master Lokasi</p>
        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Edit Lokasi</h2>
        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Perbarui identitas dan status lokasi agar sinkron dengan kebutuhan operasional.</p>

        <form action="{{ route('admin.locations.update', $location) }}" method="POST" class="mt-8">
            @method('PUT')
            @include('admin.locations._form', ['submitLabel' => 'Perbarui Lokasi'])
        </form>
    </section>
@endsection
