<?php

namespace Tests\Feature;

use App\Models\IncidentCategory;
use App\Models\IncidentReport;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\Location;
use App\Models\PotentialHazardReport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDashboardFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_dashboard_displays_report_progress_and_latest_knowledge(): void
    {
        $user = $this->createMahasiswaUser();
        $category = IncidentCategory::query()->create([
            'name' => 'Unsafe Condition',
        ]);
        $location = Location::query()->create([
            'name' => 'Workshop Teknik',
            'code' => 'WORKSHOP',
            'is_active' => true,
        ]);

        IncidentReport::query()->create([
            'report_number' => 'INC-TEST-001',
            'reported_by' => $user->id,
            'incident_category_id' => $category->id,
            'location_id' => $location->id,
            'incident_date' => '2026-04-25',
            'incident_time' => '08:30:00',
            'severity_level' => 'medium',
            'title' => 'Kabel terkelupas di area mesin',
            'chronology' => 'Ditemukan kabel terkelupas dekat mesin.',
            'status' => 'investigating',
            'submitted_at' => now()->subHour(),
        ]);

        $knowledgeCategory = KnowledgeCategory::query()->create([
            'name' => 'APD dan Peralatan',
            'slug' => 'apd-dan-peralatan',
        ]);

        KnowledgeArticle::query()->create([
            'knowledge_category_id' => $knowledgeCategory->id,
            'title' => 'Panduan APD Bengkel',
            'slug' => 'panduan-apd-bengkel',
            'summary' => 'Ringkasan penggunaan APD di area bengkel.',
            'content' => 'Gunakan kacamata, sepatu keselamatan, dan APD sesuai pekerjaan.',
            'reading_time' => '5 menit',
            'status' => 'published',
            'created_by' => $user->id,
            'approved_by' => $user->id,
            'published_at' => now(),
        ]);

        PotentialHazardReport::query()->create([
            'report_number' => 'HZD-TEST-001',
            'reported_by' => $user->id,
            'location_id' => $location->id,
            'hazard_type' => 'peralatan',
            'title' => 'Pelindung mesin retak',
            'status' => 'reviewed',
            'submitted_at' => now()->subMinutes(30),
            'response_note' => 'Satgas menjadwalkan penggantian komponen pelindung.',
        ]);

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertOk()
            ->assertSeeText('Sedang investigasi')
            ->assertSeeText('Kabel terkelupas di area mesin')
            ->assertSeeText('Status Hazard Terbaru')
            ->assertSeeText('Pelindung mesin retak')
            ->assertSeeText('Panduan APD Bengkel')
            ->assertSeeText('Unsafe Condition');
    }

    protected function createMahasiswaUser(): User
    {
        $role = Role::query()->create([
            'name' => 'Mahasiswa',
            'code' => 'mahasiswa',
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }
}
