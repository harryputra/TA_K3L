@extends('admin.layouts.app')

@section('title', 'Edit Akun')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Kelola Akun</p>
        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Edit Akun</h2>
        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Perbarui role, status, dan informasi dasar akun agar akses sistem tetap sesuai.</p>

        <form action="{{ route('admin.users.update', $managedUser) }}" method="POST" class="mt-8">
            @method('PUT')
            @include('admin.users._form', ['submitLabel' => 'Perbarui Akun'])
        </form>
    </section>
@endsection
