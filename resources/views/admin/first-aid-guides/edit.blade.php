@extends('admin.layouts.app')

@section('title', 'Edit Panduan Pertolongan Pertama')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <h2 class="text-3xl font-semibold text-slate-900">Edit Panduan Pertolongan Pertama</h2>
        <form action="{{ route('admin.first-aid-guides.update', $firstAidGuide) }}" method="POST" class="mt-8">
            @method('PUT')
            @include('admin.first-aid-guides._form', ['submitLabel' => 'Perbarui Panduan'])
        </form>
    </section>
@endsection
