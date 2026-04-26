@extends('admin.layouts.app')

@section('title', 'Edit Kontak Darurat')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <h2 class="text-3xl font-semibold text-slate-900">Edit Kontak Darurat</h2>
        <form action="{{ route('admin.emergency-contacts.update', $emergencyContact) }}" method="POST" class="mt-8">
            @method('PUT')
            @include('admin.emergency-contacts._form', ['submitLabel' => 'Perbarui Kontak'])
        </form>
    </section>
@endsection
