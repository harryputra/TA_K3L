<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\IncidentReport;
use App\Models\KnowledgeArticle;
use App\Models\PotentialHazardReport;
use Illuminate\Support\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __invoke(): View
    {
        return view('user.profile.show', $this->profileViewData());
    }

    public function edit(): View
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $studentProfile = Schema::hasTable('student_profiles') ? $user->studentProfile : null;

        return view('user.profile.edit', [
            'user' => $user,
            'studentProfile' => $studentProfile,
            'profileCard' => $this->profileViewData()['profileCard'],
        ]);
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

        if (Schema::hasTable('student_profiles') && $user->studentProfile) {
            $user->studentProfile->fill([
                'class_name' => $validated['class_name'] ?? $user->studentProfile->class_name,
            ])->save();
        }

        return redirect()
            ->route('user.profile.show')
            ->with('status', 'Profil Anda berhasil diperbarui.');
    }

    protected function profileViewData(): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (Schema::hasTable('student_profiles')) {
            $user->loadMissing('studentProfile');
        }

        if (Schema::hasTable('study_programs')) {
            $user->loadMissing('studentProfile.studyProgram');
        }

        if (Schema::hasTable('employee_profiles')) {
            $user->loadMissing('employeeProfile');
        }

        if (Schema::hasTable('departments')) {
            $user->loadMissing('employeeProfile.department');
        }

        $studentProfile = Schema::hasTable('student_profiles') ? $user->getRelation('studentProfile') : null;
        $employeeProfile = Schema::hasTable('employee_profiles') ? $user->getRelation('employeeProfile') : null;
        $studyProgram = Schema::hasTable('study_programs') ? $studentProfile?->studyProgram : null;

        $incidentCount = Schema::hasTable('incident_reports')
            ? IncidentReport::query()->where('reported_by', $user->id)->count()
            : 0;
        $hazardCount = Schema::hasTable('potential_hazard_reports')
            ? PotentialHazardReport::query()->where('reported_by', $user->id)->count()
            : 0;

        $verifiedIncidentCount = Schema::hasTable('incident_reports')
            ? IncidentReport::query()->where('reported_by', $user->id)->whereIn('status', ['verified', 'investigating', 'resolved', 'closed'])->count()
            : 0;

        $closedIncidentCount = Schema::hasTable('incident_reports')
            ? IncidentReport::query()->where('reported_by', $user->id)->where('status', 'closed')->count()
            : 0;

        $moduleCount = Schema::hasTable('knowledge_articles')
            ? KnowledgeArticle::query()->where('status', 'published')->count()
            : 0;

        $profileCard = [
            'name' => $user->name,
            'identifier' => $studentProfile?->nim ?? $employeeProfile?->nip ?? ($user->username ?: 'Belum tersedia'),
            'roleLabel' => $studyProgram?->name
                ? $studyProgram->name . ' / Tingkat ' . ($studentProfile?->entry_year ? now()->year - (int) $studentProfile->entry_year + 1 : 4)
                : ($employeeProfile?->position ?? 'Mahasiswa Aktif'),
            'email' => $user->email,
            'phone' => $user->phone ?: 'Belum tersedia',
            'medicalInfo' => 'Belum tersedia',
        ];

        $stats = [
            ['icon' => 'campaign', 'value' => $incidentCount + $hazardCount, 'label' => 'Laporan Dibuat'],
            ['icon' => 'verified', 'value' => $verifiedIncidentCount, 'label' => 'Laporan Terverifikasi'],
            ['icon' => 'school', 'value' => $moduleCount, 'label' => 'Materi K3 Tersedia'],
        ];

        $timeline = $this->buildTimeline($user->id);

        return compact('user', 'profileCard', 'stats', 'timeline', 'closedIncidentCount');
    }

    protected function buildTimeline(int $userId): Collection
    {
        $timeline = collect();

        if (Schema::hasTable('incident_reports')) {
            $incidentTimeline = IncidentReport::query()
                ->with(['category', 'location'])
                ->where('reported_by', $userId)
                ->latest()
                ->take(3)
                ->get()
                ->map(function (IncidentReport $report) {
                    return [
                        'title' => 'Laporan Insiden',
                        'description' => sprintf(
                            '%s di %s dengan status %s.',
                            $report->title,
                            $report->location?->name ?? 'lokasi tidak diketahui',
                            $this->translateIncidentStatus($report->status),
                        ),
                        'color' => '#0B5ED7',
                        'meta' => optional($report->submitted_at ?? $report->created_at)->format('d M Y, H.i') . ' WIB',
                    ];
                });

            $timeline = $timeline->concat($incidentTimeline);
        }

        if (Schema::hasTable('potential_hazard_reports')) {
            $hazardTimeline = PotentialHazardReport::query()
                ->with(['location', 'reviewer', 'resolver'])
                ->where('reported_by', $userId)
                ->latest('submitted_at')
                ->take(2)
                ->get()
                ->map(function (PotentialHazardReport $report) {
                    $handledBy = $report->resolver?->name ?? $report->reviewer?->name;

                    return [
                        'title' => 'Hazard Report',
                        'description' => sprintf(
                            '%s di %s dengan status %s%s.',
                            $report->title,
                            $report->location?->name ?? 'lokasi tidak diketahui',
                            $this->translateHazardStatus($report->status),
                            $handledBy ? ' oleh ' . $handledBy : '',
                        ),
                        'color' => '#F59E0B',
                        'meta' => optional($report->submitted_at ?? $report->created_at)->format('d M Y, H.i') . ' WIB',
                    ];
                });

            $timeline = $timeline->concat($hazardTimeline);
        }

        if (Schema::hasTable('knowledge_articles')) {
            $knowledgeTimeline = KnowledgeArticle::query()
                ->with('category')
                ->where('status', 'published')
                ->latest('published_at')
                ->take(2)
                ->get()
                ->map(function (KnowledgeArticle $article) {
                    return [
                        'title' => 'Materi K3',
                        'description' => sprintf(
                            'Materi "%s" tersedia pada kategori %s.',
                            $article->title,
                            $article->category?->name ?? 'umum',
                        ),
                        'color' => '#FF6505',
                        'meta' => optional($article->published_at)->format('d M Y') ?: 'Tanggal publikasi belum tersedia',
                    ];
                });

            $timeline = $timeline->concat($knowledgeTimeline);
        }

        if ($timeline->isEmpty()) {
            return collect([
                [
                    'title' => 'Belum Ada Aktivitas',
                    'description' => 'Aktivitas profil akan muncul setelah Anda mulai membuat laporan atau membuka materi K3 yang tersedia.',
                    'color' => '#94A3B8',
                    'meta' => 'Menunggu data',
                ],
            ]);
        }

        return $timeline->take(5)->values();
    }

    protected function translateIncidentStatus(string $status): string
    {
        return match ($status) {
            'submitted' => 'menunggu review',
            'verified' => 'terverifikasi',
            'investigating' => 'dalam investigasi',
            'resolved' => 'tindakan selesai',
            'closed' => 'ditutup',
            'rejected' => 'memerlukan perbaikan',
            default => $status,
        };
    }

    protected function translateHazardStatus(string $status): string
    {
        return match ($status) {
            'submitted' => 'menunggu review',
            'reviewed' => 'sedang ditindaklanjuti',
            'resolved' => 'sudah selesai ditangani',
            default => $status,
        };
    }
}
