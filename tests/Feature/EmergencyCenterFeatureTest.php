<?php

namespace Tests\Feature;

use App\Models\EmergencyContact;
use App\Models\EmergencyResponseStep;
use App\Models\FirstAidAction;
use App\Models\FirstAidGuide;
use App\Models\IncidentCategory;
use App\Models\IncidentReport;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmergencyCenterFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_emergency_center_displays_database_driven_content(): void
    {
        $user = $this->createMahasiswaUser();

        EmergencyContact::query()->create([
            'name' => 'Satgas Darurat',
            'phone_number' => '0800-111-222',
            'description' => 'Kontak respons cepat kampus.',
            'icon' => 'shield_person',
            'color_class' => 'bg-[var(--primary-color)]',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        EmergencyResponseStep::query()->create([
            'title' => 'Hubungi petugas',
            'description' => 'Segera hubungi petugas terdekat saat keadaan aman.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $guide = FirstAidGuide::query()->create([
            'title' => 'Paparan Gas',
            'icon' => 'air',
            'accent_class' => 'bg-sky-600',
            'summary' => 'Jauhkan korban dari sumber paparan.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        FirstAidAction::query()->create([
            'first_aid_guide_id' => $guide->id,
            'description' => 'Pindahkan korban ke area dengan ventilasi baik.',
            'sort_order' => 1,
        ]);

        $category = IncidentCategory::query()->create(['name' => 'Unsafe Condition']);
        $location = Location::query()->create([
            'name' => 'Workshop Teknik',
            'code' => 'WORKSHOP',
            'is_active' => true,
        ]);

        IncidentReport::query()->create([
            'report_number' => 'INC-EMR-001',
            'reported_by' => $user->id,
            'incident_category_id' => $category->id,
            'location_id' => $location->id,
            'incident_date' => '2026-04-25',
            'incident_time' => '10:00:00',
            'severity_level' => 'medium',
            'title' => 'Kebocoran gas ringan',
            'chronology' => 'Tercium bau gas di dekat alat praktikum dan area segera diamankan.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('user.emergency.index'))
            ->assertOk()
            ->assertSeeText('Satgas Darurat')
            ->assertSeeText('Hubungi petugas')
            ->assertSeeText('Paparan Gas')
            ->assertSeeText('Pindahkan korban ke area dengan ventilasi baik.')
            ->assertSeeText('Kebocoran gas ringan');
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
