<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\EmergencyContact;
use App\Models\EmergencyResponseStep;
use App\Models\FirstAidGuide;
use App\Models\IncidentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EmergencyCenterController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $emergencyContacts = collect();
        $responseSteps = collect();
        $firstAidGuides = collect();

        if (Schema::hasTable('emergency_contacts')) {
            $emergencyContacts = EmergencyContact::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        }

        if (Schema::hasTable('emergency_response_steps')) {
            $responseSteps = EmergencyResponseStep::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        }

        if (Schema::hasTable('first_aid_guides') && Schema::hasTable('first_aid_actions')) {
            $firstAidGuides = FirstAidGuide::query()
                ->with('actions')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        }

        $recentReports = IncidentReport::query()
            ->with(['category', 'location'])
            ->where('reported_by', $user->id)
            ->latest()
            ->take(4)
            ->get();

        return view('user.emergency.index', compact('emergencyContacts', 'responseSteps', 'firstAidGuides', 'recentReports'));
    }
}
