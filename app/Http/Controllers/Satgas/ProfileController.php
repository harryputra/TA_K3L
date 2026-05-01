<?php

namespace App\Http\Controllers\Satgas;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\IncidentFollowUp;
use App\Models\IncidentReport;
use App\Models\PotentialHazardReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (Schema::hasTable('employee_profiles')) {
            $user->loadMissing('employeeProfile.department');
        }

        $employeeProfile = $user->relationLoaded('employeeProfile') ? $user->employeeProfile : null;
        $department = $employeeProfile?->relationLoaded('department') ? $employeeProfile->department : null;
        $roleLabel = ($employeeProfile?->position ?? 'Petugas Satgas K3L') . ($department ? ' / ' . $department->name : '');

        $stats = [
            [
                'label' => 'Insiden Ditugaskan',
                'value' => Schema::hasTable('incident_reports')
                    ? IncidentReport::query()->where('assigned_satgas_id', $user->id)->count()
                    : 0,
                'icon' => 'fact_check',
                'tone' => 'text-[var(--primary-color)] bg-[var(--blue-low-opacity)]',
            ],
            [
                'label' => 'Tindak Lanjut Dibuat',
                'value' => Schema::hasTable('incident_follow_ups')
                    ? IncidentFollowUp::query()->where('created_by', $user->id)->count()
                    : 0,
                'icon' => 'task_alt',
                'tone' => 'text-[var(--green)] bg-emerald-50',
            ],
            [
                'label' => 'Hazard Direview',
                'value' => Schema::hasTable('potential_hazard_reports')
                    ? PotentialHazardReport::query()
                        ->where(fn ($query) => $query
                            ->where('reviewed_by', $user->id)
                            ->orWhere('resolved_by', $user->id))
                        ->count()
                    : 0,
                'icon' => 'warning',
                'tone' => 'text-[var(--yellow)] bg-amber-50',
            ],
        ];

        $recentAssignments = Schema::hasTable('incident_reports')
            ? IncidentReport::query()
                ->with(['category', 'location'])
                ->where('assigned_satgas_id', $user->id)
                ->latest()
                ->take(4)
                ->get()
            : collect();

        $recentHazards = Schema::hasTable('potential_hazard_reports')
            ? PotentialHazardReport::query()
                ->with('location')
                ->where(fn ($query) => $query
                    ->where('reviewed_by', $user->id)
                    ->orWhere('resolved_by', $user->id))
                ->latest('updated_at')
                ->take(4)
                ->get()
            : collect();

        return view('satgas.profile.show', compact('user', 'stats', 'recentAssignments', 'recentHazards', 'roleLabel'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'username' => $validated['username'] ?: null,
            'phone' => $validated['phone'] ?: null,
        ])->save();

        return redirect()
            ->route('satgas.profile.show')
            ->with('status', 'Profil Satgas berhasil diperbarui.');
    }
}
