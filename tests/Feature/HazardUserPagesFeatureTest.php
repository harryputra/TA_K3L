<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\PotentialHazardAttachment;
use App\Models\PotentialHazardReport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HazardUserPagesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_hazard_index_displays_user_owned_summary_counts(): void
    {
        $user = $this->createMahasiswaUser();
        $otherUser = $this->createMahasiswaUser('mahasiswa-b');
        $location = $this->createLocation();

        foreach (['submitted', 'reviewed', 'resolved'] as $index => $status) {
            PotentialHazardReport::query()->create([
                'report_number' => sprintf('HZD-USR-%03d', $index + 1),
                'reported_by' => $user->id,
                'location_id' => $location->id,
                'hazard_type' => 'lingkungan',
                'title' => "Hazard {$status}",
                'status' => $status,
                'submitted_at' => now(),
            ]);
        }

        PotentialHazardReport::query()->create([
            'report_number' => 'HZD-OTH-001',
            'reported_by' => $otherUser->id,
            'location_id' => $location->id,
            'hazard_type' => 'listrik',
            'title' => 'Hazard user lain',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('user.hazards.index'))
            ->assertOk()
            ->assertSeeText('Submitted')
            ->assertSeeText('Reviewed')
            ->assertSeeText('Resolved')
            ->assertDontSeeText('Hazard user lain');
    }

    public function test_hazard_detail_displays_satgas_response_and_attachments(): void
    {
        $user = $this->createMahasiswaUser();
        $satgas = $this->createSatgasUser();
        $location = $this->createLocation();

        $report = PotentialHazardReport::query()->create([
            'report_number' => 'HZD-DTL-001',
            'reported_by' => $user->id,
            'reviewed_by' => $satgas->id,
            'resolved_by' => $satgas->id,
            'location_id' => $location->id,
            'hazard_type' => 'peralatan',
            'title' => 'Pelindung mesin retak',
            'notes' => 'Perlu pengecekan sebelum dipakai lagi.',
            'response_note' => 'Komponen pelindung sudah diganti.',
            'status' => 'resolved',
            'submitted_at' => now()->subDay(),
            'reviewed_at' => now()->subHours(5),
            'resolved_at' => now(),
        ]);

        PotentialHazardAttachment::query()->create([
            'potential_hazard_report_id' => $report->id,
            'file_name' => 'retak.jpg',
            'file_path' => 'potential-hazard-attachments/retak.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 12000,
            'uploaded_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('user.hazards.show', $report))
            ->assertOk()
            ->assertSeeText('Komponen pelindung sudah diganti.')
            ->assertSeeText('retak.jpg')
            ->assertSeeText('resolved');
    }

    public function test_user_cannot_open_other_users_hazard_detail(): void
    {
        $user = $this->createMahasiswaUser();
        $otherUser = $this->createMahasiswaUser('mahasiswa-c');
        $location = $this->createLocation();

        $report = PotentialHazardReport::query()->create([
            'report_number' => 'HZD-PRV-001',
            'reported_by' => $otherUser->id,
            'location_id' => $location->id,
            'hazard_type' => 'listrik',
            'title' => 'Panel terbuka',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('user.hazards.show', $report))
            ->assertForbidden();
    }

    protected function createMahasiswaUser(string $code = 'mahasiswa'): User
    {
        $role = Role::query()->firstOrCreate([
            'code' => $code,
        ], [
            'name' => ucfirst(str_replace('-', ' ', $code)),
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    protected function createSatgasUser(): User
    {
        $role = Role::query()->firstOrCreate([
            'code' => 'satgas',
        ], [
            'name' => 'Satgas',
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    protected function createLocation(): Location
    {
        return Location::query()->create([
            'name' => 'Bengkel Produksi',
            'code' => 'BP-01',
            'is_active' => true,
        ]);
    }
}
