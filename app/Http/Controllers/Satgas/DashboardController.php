<?php

namespace App\Http\Controllers\Satgas;

use App\Http\Controllers\Controller;
use App\Support\Dashboard\SatgasDashboardData;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(SatgasDashboardData $dashboardData): View
    {
        [
            'stats' => $stats,
            'priorityReports' => $priorityReports,
            'workloadSummary' => $workloadSummary,
        ] = $dashboardData->build();

        return view('satgas.dashboard', compact('stats', 'priorityReports', 'workloadSummary'));
    }
}
