<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmergencyContactRequest;
use App\Http\Requests\Admin\UpdateEmergencyContactRequest;
use App\Models\EmergencyContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmergencyContactController extends Controller
{
    public function index(): View
    {
        $contacts = EmergencyContact::query()
            ->orderBy('sort_order')
            ->paginate(10);

        return view('admin.emergency-contacts.index', compact('contacts'));
    }

    public function create(): View
    {
        return view('admin.emergency-contacts.create');
    }

    public function store(StoreEmergencyContactRequest $request): RedirectResponse
    {
        EmergencyContact::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.emergency-contacts.index')
            ->with('status', 'Kontak darurat berhasil ditambahkan.');
    }

    public function edit(EmergencyContact $emergencyContact): View
    {
        return view('admin.emergency-contacts.edit', compact('emergencyContact'));
    }

    public function update(UpdateEmergencyContactRequest $request, EmergencyContact $emergencyContact): RedirectResponse
    {
        $emergencyContact->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.emergency-contacts.index')
            ->with('status', 'Kontak darurat berhasil diperbarui.');
    }

    public function destroy(EmergencyContact $emergencyContact): RedirectResponse
    {
        $emergencyContact->delete();

        return redirect()
            ->route('admin.emergency-contacts.index')
            ->with('status', 'Kontak darurat berhasil dihapus.');
    }
}
