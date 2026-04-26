<?php

namespace App\Support\Dashboard;

use App\Models\IncidentReport;

class SatgasDashboardData
{
    public function build(): array
    {
        $priorityReports = IncidentReport::query()
            ->with(['reporter', 'location', 'category'])
            ->whereIn('status', ['submitted', 'verified', 'investigating', 'resolved'])
            ->orderByRaw("
                CASE severity_level
                    WHEN 'critical' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'medium' THEN 3
                    ELSE 4
                END
            ")
            ->latest()
            ->take(6)
            ->get();

        return [
            'stats' => [
                'submitted_incidents' => IncidentReport::query()->where('status', 'submitted')->count(),
                'verified_incidents' => IncidentReport::query()->where('status', 'verified')->count(),
                'investigating_incidents' => IncidentReport::query()->where('status', 'investigating')->count(),
                'resolved_incidents' => IncidentReport::query()->where('status', 'resolved')->count(),
                'closed_incidents' => IncidentReport::query()->where('status', 'closed')->count(),
                'critical_incidents' => IncidentReport::query()->where('severity_level', 'critical')->count(),
            ],
            'priorityReports' => $priorityReports,
            'workloadSummary' => [
                'needs_review' => $priorityReports->where('status', 'submitted')->count(),
                'needs_field_follow_up' => $priorityReports->whereIn('status', ['investigating', 'resolved'])->count(),
                'ready_to_close' => IncidentReport::query()->where('status', 'resolved')->count(),
            ],
        ];
    }
}
