<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreManagedUserRequest;
use App\Http\Requests\Admin\UpdateManagedUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $selectedRole = (string) $request->query('role', '');
        $selectedStatus = (string) $request->query('status', '');
        $selectedQuery = trim((string) $request->query('q', ''));

        $users = User::query()
            ->with('role')
            ->when($selectedRole !== '', fn ($query) => $query->whereHas('role', fn ($roleQuery) => $roleQuery->where('code', $selectedRole)))
            ->when($selectedStatus !== '', fn ($query) => $query->where('is_active', $selectedStatus === 'active'))
            ->when($selectedQuery !== '', function ($query) use ($selectedQuery) {
                $query->where(function ($subQuery) use ($selectedQuery) {
                    $subQuery
                        ->where('name', 'like', '%' . $selectedQuery . '%')
                        ->orWhere('email', 'like', '%' . $selectedQuery . '%')
                        ->orWhere('username', 'like', '%' . $selectedQuery . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $roles = Role::query()->orderBy('name')->get();
        $summary = [
            'total' => User::query()->count(),
            'admin' => User::query()->whereHas('role', fn ($query) => $query->where('code', 'admin'))->count(),
            'satgas' => User::query()->whereHas('role', fn ($query) => $query->where('code', 'satgas'))->count(),
            'mahasiswa' => User::query()->whereHas('role', fn ($query) => $query->where('code', 'mahasiswa'))->count(),
            'active' => User::query()->where('is_active', true)->count(),
        ];

        return view('admin.users.index', compact('users', 'roles', 'summary', 'selectedRole', 'selectedStatus', 'selectedQuery'));
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => Role::query()->orderBy('name')->get(),
            'managedUser' => new User(),
        ]);
    }

    public function store(StoreManagedUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::query()->create([
            'role_id' => $validated['role_id'],
            'name' => $validated['name'],
            'username' => $validated['username'] ?: null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?: null,
            'password' => $validated['password'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Akun baru berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $user->load('role');

        return view('admin.users.edit', [
            'managedUser' => $user,
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateManagedUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $payload = [
            'role_id' => $validated['role_id'],
            'name' => $validated['name'],
            'username' => $validated['username'] ?: null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?: null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Data akun berhasil diperbarui.');
    }
}
