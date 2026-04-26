<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\PotentialHazardAttachment;
use App\Models\PotentialHazardReport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminHazardMonitoringFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_hazard_index_displays_database_driven_summary_and_listing(): void
    {
        $admin = $this->createAdminUser();
        $reporter = $this->createMahasiswaUser();
        $satgas = $this->createSatgasUser();
        $location = $this->createLocation();

        PotentialHazardReport::query()->create([
            'report_number' => 'HZD-ADM-001',
            'reported_by' => $reporter->id,
            'location_id' => $location->id,
            'hazard_type' => 'lingkungan',
            'title' => 'Genangan air',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        PotentialHazardReport::query()->create([
            'report_number' => 'HZD-ADM-002',
            'reported_by' => $reporter->id,
            'reviewed_by' => $satgas->id,
            'location_id' => $location->id,
            'hazard_type' => 'listrik',
            'title' => 'Stop kontak longgar',
            'status' => 'reviewed',
            'submitted_at' => now(),
            'reviewed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.hazards.index'))
            ->assertOk()
            ->assertSeeText('Submitted')
            ->assertSeeText('Reviewed')
            ->assertSeeText('Resolved')
            ->assertSeeText('Genangan air')
            ->assertSeeText('Stop kontak longgar');
    }

    public function test_admin_hazard_detail_displays_satgas_metadata_and_attachments(): void
    {
        $admin = $this->createAdminUser();
        $reporter = $this->createMahasiswaUser();
        $satgas = $this->createSatgasUser();
        $location = $this->createLocation();

        $report = PotentialHazardReport::query()->create([
            'report_number' => 'HZD-ADM-003',
            'reported_by' => $reporter->id,
            'reviewed_by' => $satgas->id,
            'resolved_by' => $satgas->id,
            'location_id' => $location->id,
            'hazard_type' => 'peralatan',
            'title' => 'Cover mesin terbuka',
            'response_note' => 'Mesin dihentikan sementara dan cover dipasang ulang.',
            'status' => 'resolved',
            'submitted_at' => now()->subDay(),
            'reviewed_at' => now()->subHours(10),
            'resolved_at' => now(),
        ]);

        PotentialHazardAttachment::query()->create([
            'potential_hazard_report_id' => $report->id,
            'file_name' => 'cover.jpg',
            'file_path' => 'potential-hazard-attachments/cover.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 9000,
            'uploaded_by' => $reporter->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.hazards.show', $report))
            ->assertOk()
            ->assertSeeText('Mesin dihentikan sementara dan cover dipasang ulang.')
            ->assertSeeText('cover.jpg')
            ->assertSeeText($satgas->name);
    }

    protected function createAdminUser(): User
    {
        $role = Role::query()->firstOrCreate(['code' => 'admin'], ['name' => 'Admin']);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    protected function createMahasiswaUser(): User
    {
        $role = Role::query()->firstOrCreate(['code' => 'mahasiswa'], ['name' => 'Mahasiswa']);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    protected function createSatgasUser(): User
    {
        $role = Role::query()->firstOrCreate(['code' => 'satgas'], ['name' => 'Satgas']);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    protected function createLocation(): Location
    {
        return Location::query()->create([
            'name' => 'Laboratorium Material',
            'code' => 'LM-01',
            'is_active' => true,
        ]);
    }
}
