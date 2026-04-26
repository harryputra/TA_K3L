@extends('admin.layouts.app')

@section('title', 'Edit Langkah Tanggap Cepat')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <h2 class="text-3xl font-semibold text-slate-900">Edit Langkah Tanggap Cepat</h2>
        <form action="{{ route('admin.emergency-response-steps.update', $emergencyResponseStep) }}" method="POST" class="mt-8">
            @method('PUT')
            @include('admin.emergency-response-steps._form', ['submitLabel' => 'Perbarui Langkah'])
        </form>
    </section>
@endsection
