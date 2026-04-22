<?php

namespace App\Support\Dashboard;

use App\Models\IncidentReport;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;

class AdminDashboardData
{
    public function build(): array
    {
        return [
            'stats' => [
                'total_users' => User::query()->count(),
                'active_users' => User::query()->where('is_active', true)->count(),
                'satgas_count' => User::query()->whereHas('role', fn ($query) => $query->where('code', 'satgas'))->count(),
                'incident_count' => IncidentReport::query()->count(),
                'submitted_incidents' => IncidentReport::query()->where('status', 'submitted')->count(),
                'verified_incidents' => IncidentReport::query()->where('status', 'verified')->count(),
                'location_count' => Location::query()->count(),
                'role_count' => Role::query()->count(),
            ],
            'recentReports' => IncidentReport::query()
                ->with(['reporter', 'category', 'location'])
                ->latest()
                ->take(5)
                ->get(),
        ];
    }
}
