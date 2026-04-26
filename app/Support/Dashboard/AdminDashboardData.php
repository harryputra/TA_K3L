<?php

namespace App\Support\Dashboard;

use App\Models\IncidentReport;
use App\Models\KnowledgeArticle;
use App\Models\Location;
use App\Models\PotentialHazardReport;
use App\Models\Role;
use App\Models\User;
use App\Models\EmergencyContact;
use Illuminate\Support\Facades\Schema;

class AdminDashboardData
{
    public function build(): array
    {
        $publishedKnowledgeCount = class_exists(KnowledgeArticle::class) && Schema::hasTable('knowledge_articles')
            ? KnowledgeArticle::query()->where('status', 'published')->count()
            : 0;

        $hazardReportCount = class_exists(PotentialHazardReport::class) && Schema::hasTable('potential_hazard_reports')
            ? PotentialHazardReport::query()->count()
            : 0;

        $activeEmergencyContacts = class_exists(EmergencyContact::class) && Schema::hasTable('emergency_contacts')
            ? EmergencyContact::query()->where('is_active', true)->count()
            : 0;

        $reviewedHazards = class_exists(PotentialHazardReport::class) && Schema::hasTable('potential_hazard_reports')
            ? PotentialHazardReport::query()->where('status', 'reviewed')->count()
            : 0;

        $resolvedHazards = class_exists(PotentialHazardReport::class) && Schema::hasTable('potential_hazard_reports')
            ? PotentialHazardReport::query()->where('status', 'resolved')->count()
            : 0;

        $recentHazardReports = class_exists(PotentialHazardReport::class) && Schema::hasTable('potential_hazard_reports')
            ? PotentialHazardReport::query()
                ->with(['reporter', 'location', 'reviewer', 'resolver'])
                ->latest('submitted_at')
                ->take(5)
                ->get()
            : collect();

        $recentIncidentReports = IncidentReport::query()
            ->with(['reporter', 'category', 'location'])
            ->latest()
            ->take(5)
            ->get();

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
                'published_knowledge' => $publishedKnowledgeCount,
                'hazard_reports' => $hazardReportCount,
                'reviewed_hazards' => $reviewedHazards,
                'resolved_hazards' => $resolvedHazards,
                'emergency_contacts' => $activeEmergencyContacts,
            ],
            'recentReports' => $recentIncidentReports,
            'recentHazardReports' => $recentHazardReports,
            'operationalSummary' => [
                'incident_backlog' => IncidentReport::query()->whereIn('status', ['submitted', 'verified', 'investigating'])->count(),
                'hazard_backlog' => class_exists(PotentialHazardReport::class) && Schema::hasTable('potential_hazard_reports')
                    ? PotentialHazardReport::query()->whereIn('status', ['submitted', 'reviewed'])->count()
                    : 0,
                'published_knowledge' => $publishedKnowledgeCount,
                'active_emergency_contacts' => $activeEmergencyContacts,
            ],
        ];
    }
}
