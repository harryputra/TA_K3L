<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\EmergencyContact;
use App\Models\KnowledgeArticle;
use App\Support\Dashboard\UserDashboardData;
use App\Support\Hazards\PublicHazardMapData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, UserDashboardData $dashboardData, PublicHazardMapData $hazardMapData): View
    {
        $userDashboard = $request->user()
            ? $dashboardData->build($request->user()->id)
            : null;

        $knowledgeQuery = class_exists(KnowledgeArticle::class) && Schema::hasTable('knowledge_articles')
            ? KnowledgeArticle::query()->with('category')->where('status', 'published')
            : null;

        $publishedKnowledgeCount = $userDashboard['publishedKnowledgeCount']
            ?? ($knowledgeQuery ? (clone $knowledgeQuery)->count() : 0);
        $featuredKnowledge = $userDashboard['featuredKnowledge']
            ?? ($knowledgeQuery ? (clone $knowledgeQuery)->latest('published_at')->first() : null);
        $knowledgeRecommendations = $userDashboard['knowledgeRecommendations']
            ?? ($knowledgeQuery ? (clone $knowledgeQuery)->latest('published_at')->take(3)->get() : collect());
        $emergencyContacts = Schema::hasTable('emergency_contacts')
            ? EmergencyContact::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->take(3)
                ->get()
            : collect();
            
        [
            'hazardMarkers' => $hazardMarkers,
            'incidentMarkers' => $incidentMarkers,
            'floorplanMarkers' => $floorplanMarkers,
            'summaryCounts' => $summaryCounts,
            'campusBuildingPolygons' => $campusBuildingPolygons,
        ] = $hazardMapData->build();

        // ============ TAMBAHKAN INI ============
        // Hitung summary untuk hazard
        $hazardMapCounts = [
            'total' => $hazardMarkers->count(),
            'high' => $hazardMarkers->whereIn('risk_level', ['tinggi', 'kritis'])->count(),
            'active' => $hazardMarkers->where('status', '!=', 'resolved')->count(),
        ];
        
        // Hitung summary untuk incident
        $incidentMapCounts = [
            'total' => $incidentMarkers->count(),
            'high' => $incidentMarkers->whereIn('severity_level', ['high', 'critical'])->count(),
            'active' => $incidentMarkers->whereNotIn('status', ['resolved', 'closed', 'rejected'])->count(),
        ];
        // =====================================

        return view('user.dashboard', compact(
            'userDashboard',
            'publishedKnowledgeCount',
            'featuredKnowledge',
            'knowledgeRecommendations',
            'emergencyContacts',
            'hazardMarkers',
            'incidentMarkers',
            'floorplanMarkers',
            'summaryCounts',
            'campusBuildingPolygons',
            'hazardMapCounts',      
            'incidentMapCounts'     
        ));
    }
}