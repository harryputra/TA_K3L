<?php

namespace App\Support\Dashboard;

use App\Models\IncidentReport;
use App\Models\KnowledgeArticle;

class UserDashboardData
{
    public function build(int $userId): array
    {
        return [
            'stats' => [
                'my_reports' => IncidentReport::query()->where('reported_by', $userId)->count(),
                'submitted_reports' => IncidentReport::query()->where('reported_by', $userId)->where('status', 'submitted')->count(),
                'verified_reports' => IncidentReport::query()->where('reported_by', $userId)->where('status', 'verified')->count(),
                'closed_reports' => IncidentReport::query()->where('reported_by', $userId)->where('status', 'closed')->count(),
            ],
            'recentReports' => IncidentReport::query()
                ->with(['category', 'location'])
                ->where('reported_by', $userId)
                ->latest()
                ->take(5)
                ->get(),
            'publishedKnowledgeCount' => class_exists(KnowledgeArticle::class)
                ? KnowledgeArticle::query()->where('status', 'published')->count()
                : 0,
        ];
    }
}
