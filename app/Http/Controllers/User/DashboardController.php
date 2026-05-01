<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\EmergencyContact;
use App\Models\KnowledgeArticle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $publishedKnowledgeCount = KnowledgeArticle::query()
            ->where('status', 'published')
            ->count();
        $featuredKnowledge = KnowledgeArticle::query()
            ->with('category')
            ->where('status', 'published')
            ->latest('published_at')
            ->first();
        $knowledgeRecommendations = KnowledgeArticle::query()
            ->with('category')
            ->where('status', 'published')
            ->latest('published_at')
            ->take(3)
            ->get();
        $emergencyContacts = EmergencyContact::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        return view('user.dashboard', compact(
            'publishedKnowledgeCount',
            'featuredKnowledge',
            'knowledgeRecommendations',
            'emergencyContacts',
        ));
    }
}
