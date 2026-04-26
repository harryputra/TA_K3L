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
            'recentHazardReports' => $recentHazardReports,
            'publishedKnowledgeCount' => $publishedKnowledgeCount,
            'latestReportSummary' => $latestReportSummary,
            'latestHazardSummary' => $latestHazardSummary,
            'featuredKnowledge' => $featuredKnowledge,
            'knowledgeRecommendations' => $knowledgeRecommendations,
        ] = $dashboardData->build($user->id);

        return view('user.dashboard', compact(
            'stats',
            'recentReports',
            'recentHazardReports',
            'publishedKnowledgeCount',
            'latestReportSummary',
            'latestHazardSummary',
            'featuredKnowledge',
            'knowledgeRecommendations',
        ));
    }
}
