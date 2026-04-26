@extends('admin.layouts.app')

@section('title', 'Tambah Panduan Pertolongan Pertama')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <h2 class="text-3xl font-semibold text-slate-900">Tambah Panduan Pertolongan Pertama</h2>
        <form action="{{ route('admin.first-aid-guides.store') }}" method="POST" class="mt-8">
            @include('admin.first-aid-guides._form', ['submitLabel' => 'Simpan Panduan'])
        </form>
    </section>
@endsection
