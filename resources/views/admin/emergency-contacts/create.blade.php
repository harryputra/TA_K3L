@extends('admin.layouts.app')

@section('title', 'Tambah Kontak Darurat')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <h2 class="text-3xl font-semibold text-slate-900">Tambah Kontak Darurat</h2>
        <form action="{{ route('admin.emergency-contacts.store') }}" method="POST" class="mt-8">
            @include('admin.emergency-contacts._form', ['submitLabel' => 'Simpan Kontak'])
        </form>
    </section>
@endsection
