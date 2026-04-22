<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Support\Dashboard\UserDashboardData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, UserDashboardData $dashboardData): View
    {
        $user = $request->user();

        [
            'stats' => $stats,
            'recentReports' => $recentReports,
            'publishedKnowledgeCount' => $publishedKnowledgeCount,
        ] = $dashboardData->build($user->id);

        return view('user.dashboard', compact('stats', 'recentReports', 'publishedKnowledgeCount'));
    }
}
