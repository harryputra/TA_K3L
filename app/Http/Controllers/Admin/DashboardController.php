<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Dashboard\AdminDashboardData;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(AdminDashboardData $dashboardData): View
    {
        [
            'stats' => $stats,
            'recentReports' => $recentReports,
            'recentHazardReports' => $recentHazardReports,
            'operationalSummary' => $operationalSummary,
        ] = $dashboardData->build();

        return view('admin.dashboard', compact('stats', 'recentReports', 'recentHazardReports', 'operationalSummary'));
    }
}
