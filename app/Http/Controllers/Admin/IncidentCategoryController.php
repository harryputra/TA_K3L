<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreIncidentCategoryRequest;
use App\Http\Requests\Admin\UpdateIncidentCategoryRequest;
use App\Models\IncidentCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class IncidentCategoryController extends Controller
{
    public function index(): View
    {
        $incidentCategories = IncidentCategory::query()
            ->withCount('incidentReports')
            ->latest()
            ->paginate(10);

        return view('admin.incident-categories.index', compact('incidentCategories'));
    }

    public function create(): View
    {
        return view('admin.incident-categories.create');
    }

    public function store(StoreIncidentCategoryRequest $request): RedirectResponse
    {
        IncidentCategory::query()->create($request->validated());

        return redirect()
            ->route('admin.incident-categories.index')
            ->with('status', 'Kategori insiden baru berhasil ditambahkan.');
    }

    public function edit(IncidentCategory $incidentCategory): View
    {
        return view('admin.incident-categories.edit', compact('incidentCategory'));
    }

    public function update(UpdateIncidentCategoryRequest $request, IncidentCategory $incidentCategory): RedirectResponse
    {
        $incidentCategory->update($request->validated());

        return redirect()
            ->route('admin.incident-categories.index')
            ->with('status', 'Kategori insiden berhasil diperbarui.');
    }

    public function destroy(IncidentCategory $incidentCategory): RedirectResponse
    {
        if ($incidentCategory->incidentReports()->exists()) {
            return redirect()
                ->route('admin.incident-categories.index')
                ->withErrors([
                    'incident_category' => 'Kategori insiden tidak bisa dihapus karena masih dipakai oleh laporan.',
                ]);
        }

        $incidentCategory->delete();

        return redirect()
            ->route('admin.incident-categories.index')
            ->with('status', 'Kategori insiden berhasil dihapus.');
    }
}
