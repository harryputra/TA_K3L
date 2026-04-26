@extends('admin.layouts.app')

@section('title', 'Tambah Akun')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Kelola Akun</p>
        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Tambah Akun Baru</h2>
        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Buat akun baru untuk admin, satgas, atau mahasiswa dari panel administrasi.</p>

        <form action="{{ route('admin.users.store') }}" method="POST" class="mt-8">
            @include('admin.users._form', ['submitLabel' => 'Simpan Akun'])
        </form>
    </section>
@endsection
