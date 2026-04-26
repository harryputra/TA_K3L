<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLocationRequest;
use App\Http\Requests\Admin\UpdateLocationRequest;
use App\Models\Location;
use App\Models\PotentialHazardReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $locations = Location::query()
            ->latest()
            ->paginate(10);

        return view('admin.locations.index', compact('locations'));
    }

    public function create(): View
    {
        return view('admin.locations.create');
    }

    public function store(StoreLocationRequest $request): RedirectResponse
    {
        Location::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.locations.index')
            ->with('status', 'Lokasi baru berhasil ditambahkan.');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(UpdateLocationRequest $request, Location $location): RedirectResponse
    {
        $location->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.locations.index')
            ->with('status', 'Data lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $incidentUsage = $location->incidentReports()->count();
        $hazardUsage = class_exists(PotentialHazardReport::class) && Schema::hasTable('potential_hazard_reports')
            ? PotentialHazardReport::query()->where('location_id', $location->id)->count()
            : 0;
        $totalUsage = $incidentUsage + $hazardUsage;

        if ($totalUsage > 0) {
            if ($location->is_active) {
                $location->update(['is_active' => false]);

                return redirect()
                    ->route('admin.locations.index')
                    ->with('status', 'Lokasi sedang dipakai oleh data lain sehingga dinonaktifkan, bukan dihapus.');
            }

            return redirect()
                ->route('admin.locations.index')
                ->withErrors([
                    'location' => 'Lokasi tidak bisa dihapus karena masih dipakai oleh laporan atau hazard report.',
                ]);
        }

        $location->delete();

        return redirect()
            ->route('admin.locations.index')
            ->with('status', 'Lokasi berhasil dihapus.');
    }
}
