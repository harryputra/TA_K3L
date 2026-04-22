<?php

namespace App\Support\Dashboard;

use App\Models\IncidentReport;

class SatgasDashboardData
{
    public function build(): array
    {
        return [
            'stats' => [
                'submitted_incidents' => IncidentReport::query()->where('status', 'submitted')->count(),
                'verified_incidents' => IncidentReport::query()->where('status', 'verified')->count(),
                'investigating_incidents' => IncidentReport::query()->where('status', 'investigating')->count(),
                'critical_incidents' => IncidentReport::query()->where('severity_level', 'critical')->count(),
            ],
            'priorityReports' => IncidentReport::query()
                ->with(['reporter', 'location'])
                ->whereIn('status', ['submitted', 'investigating'])
                ->orderByRaw("FIELD(severity_level, 'critical', 'high', 'medium', 'low')")
                ->latest()
                ->take(6)
                ->get(),
        ];
    }
}
