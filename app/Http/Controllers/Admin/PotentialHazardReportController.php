<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PotentialHazardReport;
use Illuminate\View\View;

class PotentialHazardReportController extends Controller
{
    public function index(): View
    {
        $reports = PotentialHazardReport::query()
            ->with(['reporter', 'location', 'reviewer', 'resolver'])
            ->latest('submitted_at')
            ->paginate(10);

        $summaryCounts = collect(['submitted', 'reviewed', 'resolved'])
            ->mapWithKeys(fn (string $status) => [
                $status => PotentialHazardReport::query()->where('status', $status)->count(),
            ])
            ->all();

        return view('admin.hazards.index', compact('reports', 'summaryCounts'));
    }

    public function show(PotentialHazardReport $potentialHazardReport): View
    {
        $potentialHazardReport->load([
            'reporter.role',
            'location',
            'attachments',
            'reviewer',
            'resolver',
        ]);

        return view('admin.hazards.show', [
            'hazardReport' => $potentialHazardReport,
        ]);
    }
}
