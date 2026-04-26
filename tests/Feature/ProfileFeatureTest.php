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

class ProfileFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_displays_database_driven_stats_and_timeline(): void
    {
        $user = $this->createMahasiswaUser();
        $category = IncidentCategory::query()->create([
            'name' => 'Unsafe Action',
        ]);
        $location = Location::query()->create([
            'name' => 'Laboratorium Kimia',
            'code' => 'LAB-KIM',
            'is_active' => true,
        ]);

        IncidentReport::query()->create([
            'report_number' => 'INC-PROFILE-001',
            'reported_by' => $user->id,
            'incident_category_id' => $category->id,
            'location_id' => $location->id,
            'incident_date' => '2026-04-25',
            'incident_time' => '09:00:00',
            'severity_level' => 'low',
            'title' => 'Tumpahan kecil bahan praktikum',
            'chronology' => 'Terjadi tumpahan kecil saat persiapan praktikum dan langsung diamankan.',
            'status' => 'verified',
            'submitted_at' => now()->subDay(),
        ]);

        $knowledgeCategory = KnowledgeCategory::query()->create([
            'name' => 'Tanggap Darurat',
            'slug' => 'tanggap-darurat',
        ]);

        KnowledgeArticle::query()->create([
            'knowledge_category_id' => $knowledgeCategory->id,
            'title' => 'Panduan Evakuasi Dasar',
            'slug' => 'panduan-evakuasi-dasar',
            'summary' => 'Langkah awal saat evakuasi.',
            'content' => 'Ikuti jalur evakuasi yang ditetapkan.',
            'reading_time' => '4 menit',
            'status' => 'published',
            'created_by' => $user->id,
            'approved_by' => $user->id,
            'published_at' => now(),
        ]);

        PotentialHazardReport::query()->create([
            'report_number' => 'HZD-PROFILE-001',
            'reported_by' => $user->id,
            'location_id' => $location->id,
            'hazard_type' => 'listrik',
            'title' => 'Kabel terbuka dekat meja kerja',
            'status' => 'reviewed',
            'submitted_at' => now()->subHours(8),
            'response_note' => 'Satgas sudah memberi pembatas area.',
        ]);

        $this->actingAs($user)
            ->get(route('user.profile.show'))
            ->assertOk()
            ->assertSeeText('Laporan Dibuat')
            ->assertSeeText('Laporan Terverifikasi')
            ->assertSeeText('Materi K3 Tersedia')
            ->assertSeeText('Tumpahan kecil bahan praktikum')
            ->assertSeeText('Kabel terbuka dekat meja kerja')
            ->assertSeeText('Panduan Evakuasi Dasar');
    }

    public function test_user_can_update_basic_profile_information(): void
    {
        $user = $this->createMahasiswaUser([
            'name' => 'Mahasiswa Lama',
            'username' => 'mhslama',
            'phone' => '081234567890',
        ]);

        $this->actingAs($user)
            ->patch(route('user.profile.update'), [
                'name' => 'Mahasiswa Baru',
                'username' => 'mhsbaru',
                'phone' => '081111111111',
            ])
            ->assertRedirect(route('user.profile.show'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Mahasiswa Baru',
            'username' => 'mhsbaru',
            'phone' => '081111111111',
        ]);
    }

    protected function createMahasiswaUser(array $attributes = []): User
    {
        $role = Role::query()->create([
            'name' => 'Mahasiswa',
            'code' => 'mahasiswa',
        ]);

        return User::factory()->create($attributes + [
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }
}
