<?php

namespace Tests\Feature;

use App\Models\IncidentCategory;
use App\Models\IncidentReport;
use App\Models\Location;
use App\Models\PotentialHazardReport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMasterDataFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_deactivates_active_location_when_it_is_still_used(): void
    {
        [$admin, $reporter] = $this->createAdminAndReporter();

        $location = Location::query()->create([
            'name' => 'Gedung Bengkel',
            'code' => 'GB-01',
            'is_active' => true,
        ]);

        $category = IncidentCategory::query()->create([
            'name' => 'Unsafe Condition',
        ]);

        IncidentReport::query()->create([
            'report_number' => 'INC-LOC-001',
            'reported_by' => $reporter->id,
            'incident_category_id' => $category->id,
            'location_id' => $location->id,
            'incident_date' => '2026-04-25',
            'incident_time' => '08:00:00',
            'severity_level' => 'medium',
            'title' => 'Area kerja licin',
            'chronology' => 'Ditemukan lantai licin di area kerja.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.locations.destroy', $location))
            ->assertRedirect(route('admin.locations.index'))
            ->assertSessionHas('status', 'Lokasi sedang dipakai oleh data lain sehingga dinonaktifkan, bukan dihapus.');

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'is_active' => false,
        ]);
    }

    public function test_admin_cannot_delete_inactive_location_that_is_still_used(): void
    {
        [$admin, $reporter] = $this->createAdminAndReporter();

        $location = Location::query()->create([
            'name' => 'Koridor Timur',
            'code' => 'KT-01',
            'is_active' => false,
        ]);

        PotentialHazardReport::query()->create([
            'report_number' => 'HZD-LOC-001',
            'reported_by' => $reporter->id,
            'location_id' => $location->id,
            'hazard_type' => 'lingkungan',
            'title' => 'Pencahayaan redup',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.locations.destroy', $location))
            ->assertRedirect(route('admin.locations.index'))
            ->assertSessionHasErrors('location');

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_unused_location(): void
    {
        [$admin] = $this->createAdminAndReporter();

        $location = Location::query()->create([
            'name' => 'Gudang APD',
            'code' => 'GAPD',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.locations.destroy', $location))
            ->assertRedirect(route('admin.locations.index'))
            ->assertSessionHas('status', 'Lokasi berhasil dihapus.');

        $this->assertDatabaseMissing('locations', [
            'id' => $location->id,
        ]);
    }

    public function test_admin_cannot_delete_incident_category_that_is_still_used(): void
    {
        [$admin, $reporter] = $this->createAdminAndReporter();

        $location = Location::query()->create([
            'name' => 'Laboratorium Dasar',
            'code' => 'LAB-DASAR',
            'is_active' => true,
        ]);

        $category = IncidentCategory::query()->create([
            'name' => 'Unsafe Action',
        ]);

        IncidentReport::query()->create([
            'report_number' => 'INC-CAT-001',
            'reported_by' => $reporter->id,
            'incident_category_id' => $category->id,
            'location_id' => $location->id,
            'incident_date' => '2026-04-25',
            'incident_time' => '10:15:00',
            'severity_level' => 'low',
            'title' => 'APD tidak lengkap',
            'chronology' => 'Pengguna area belum memakai APD lengkap.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.incident-categories.destroy', $category))
            ->assertRedirect(route('admin.incident-categories.index'))
            ->assertSessionHasErrors('incident_category');

        $this->assertDatabaseHas('incident_categories', [
            'id' => $category->id,
        ]);
    }

    public function test_admin_can_delete_unused_incident_category(): void
    {
        [$admin] = $this->createAdminAndReporter();

        $category = IncidentCategory::query()->create([
            'name' => 'Near Miss',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.incident-categories.destroy', $category))
            ->assertRedirect(route('admin.incident-categories.index'))
            ->assertSessionHas('status', 'Kategori insiden berhasil dihapus.');

        $this->assertDatabaseMissing('incident_categories', [
            'id' => $category->id,
        ]);
    }

    /**
     * @return array{0: User, 1: User}
     */
    protected function createAdminAndReporter(): array
    {
        $adminRole = Role::query()->create(['name' => 'Admin', 'code' => 'admin']);
        $mahasiswaRole = Role::query()->create(['name' => 'Mahasiswa', 'code' => 'mahasiswa']);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $reporter = User::factory()->create([
            'role_id' => $mahasiswaRole->id,
            'is_active' => true,
        ]);

        return [$admin, $reporter];
    }
}
