<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmergencyResponseStepRequest;
use App\Http\Requests\Admin\UpdateEmergencyResponseStepRequest;
use App\Models\EmergencyResponseStep;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmergencyResponseStepController extends Controller
{
    public function index(): View
    {
        $steps = EmergencyResponseStep::query()
            ->orderBy('sort_order')
            ->paginate(10);

        return view('admin.emergency-response-steps.index', compact('steps'));
    }

    public function create(): View
    {
        return view('admin.emergency-response-steps.create');
    }

    public function store(StoreEmergencyResponseStepRequest $request): RedirectResponse
    {
        EmergencyResponseStep::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.emergency-response-steps.index')
            ->with('status', 'Langkah tanggap cepat berhasil ditambahkan.');
    }

    public function edit(EmergencyResponseStep $emergencyResponseStep): View
    {
        return view('admin.emergency-response-steps.edit', compact('emergencyResponseStep'));
    }

    public function update(UpdateEmergencyResponseStepRequest $request, EmergencyResponseStep $emergencyResponseStep): RedirectResponse
    {
        $emergencyResponseStep->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.emergency-response-steps.index')
            ->with('status', 'Langkah tanggap cepat berhasil diperbarui.');
    }

    public function destroy(EmergencyResponseStep $emergencyResponseStep): RedirectResponse
    {
        $emergencyResponseStep->delete();

        return redirect()
            ->route('admin.emergency-response-steps.index')
            ->with('status', 'Langkah tanggap cepat berhasil dihapus.');
    }
}
