@csrf

<div class="grid gap-6">
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="role_id" class="mb-2 block text-sm font-semibold text-slate-800">Role akun</label>
            <select id="role_id" name="role_id"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                <option value="">Pilih role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected(old('role_id', $managedUser->role_id ?? null) == $role->id)>{{ $role->name }}</option>
                @endforeach
            </select>
            @error('role_id')
                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="mb-2 block text-sm font-semibold text-slate-800">Nama lengkap</label>
            <input id="name" name="name" type="text" value="{{ old('name', $managedUser->name ?? '') }}"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
            @error('name')
                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="username" class="mb-2 block text-sm font-semibold text-slate-800">Username</label>
            <input id="username" name="username" type="text" value="{{ old('username', $managedUser->username ?? '') }}"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
            @error('username')
                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-slate-800">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $managedUser->email ?? '') }}"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
            @error('email')
                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="phone" class="mb-2 block text-sm font-semibold text-slate-800">Nomor telepon</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone', $managedUser->phone ?? '') }}"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
            @error('phone')
                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-2 block text-sm font-semibold text-slate-800">
                {{ isset($managedUser->id) ? 'Password baru (opsional)' : 'Password awal' }}
            </label>
            <input id="password" name="password" type="password"
                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
            @error('password')
                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <label class="flex items-center gap-3 text-sm text-slate-700">
        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-[var(--primary-color)] focus:ring-[var(--primary-color)]/30"
            @checked(old('is_active', $managedUser->is_active ?? true))>
        Akun aktif dan dapat masuk ke sistem
    </label>

    <div class="rounded-[1.4rem] bg-[#f8fbff] px-5 py-4 ring-1 ring-[var(--primary-color)]/8">
        <p class="text-sm leading-7 text-slate-700">
            Admin dapat mengelola akun mahasiswa, satgas, maupun akun admin lainnya dari form ini. Gunakan role untuk menentukan akses dashboard dan modul yang tersedia.
        </p>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="inline-flex rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
            {{ $submitLabel }}
        </button>
    </div>
</div>
