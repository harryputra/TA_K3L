<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\PotentialHazardAttachment;
use App\Models\PotentialHazardReport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SatgasPotentialHazardReviewFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_satgas_hazard_index_displays_status_summary_cards(): void
    {
        $satgas = $this->createSatgasUser();
        $reporter = $this->createMahasiswaUser();
        $location = $this->createLocation();

        foreach (['submitted', 'reviewed', 'resolved'] as $index => $status) {
            PotentialHazardReport::query()->create([
                'report_number' => sprintf('HZD-SAT-%03d', $index + 1),
                'reported_by' => $reporter->id,
                'location_id' => $location->id,
                'hazard_type' => 'lingkungan',
                'title' => "Hazard {$status}",
                'status' => $status,
                'submitted_at' => now(),
            ]);
        }

        $this->actingAs($satgas)
            ->get(route('satgas.hazards.index'))
            ->assertOk()
            ->assertSeeText('Submitted')
            ->assertSeeText('Reviewed')
            ->assertSeeText('Resolved');
    }

    public function test_satgas_can_review_hazard_report_and_record_metadata(): void
    {
        $satgas = $this->createSatgasUser();
        $reporter = $this->createMahasiswaUser();
        $location = $this->createLocation();

        $report = PotentialHazardReport::query()->create([
            'report_number' => 'HZD-REV-001',
            'reported_by' => $reporter->id,
            'location_id' => $location->id,
            'hazard_type' => 'listrik',
            'title' => 'Panel terbuka',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($satgas)
            ->patch(route('satgas.hazards.update-status', $report), [
                'status' => 'reviewed',
                'response_note' => 'Area diberi pembatas dan inspeksi awal dijadwalkan.',
            ])
            ->assertRedirect(route('satgas.hazards.show', $report))
            ->assertSessionHas('status', 'Status hazard report berhasil diperbarui.');

        $this->assertDatabaseHas('potential_hazard_reports', [
            'id' => $report->id,
            'status' => 'reviewed',
            'reviewed_by' => $satgas->id,
            'response_note' => 'Area diberi pembatas dan inspeksi awal dijadwalkan.',
        ]);
    }

    public function test_satgas_hazard_detail_displays_attachments_and_final_state_message(): void
    {
        $satgas = $this->createSatgasUser();
        $reporter = $this->createMahasiswaUser();
        $location = $this->createLocation();

        $report = PotentialHazardReport::query()->create([
            'report_number' => 'HZD-RES-001',
            'reported_by' => $reporter->id,
            'location_id' => $location->id,
            'hazard_type' => 'peralatan',
            'title' => 'Pelindung mesin lepas',
            'status' => 'resolved',
            'submitted_at' => now()->subDay(),
            'reviewed_by' => $satgas->id,
            'resolved_by' => $satgas->id,
            'reviewed_at' => now()->subHours(8),
            'resolved_at' => now(),
            'response_note' => 'Pelindung mesin sudah dipasang kembali.',
        ]);

        PotentialHazardAttachment::query()->create([
            'potential_hazard_report_id' => $report->id,
            'file_name' => 'mesin.jpg',
            'file_path' => 'potential-hazard-attachments/mesin.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 12000,
            'uploaded_by' => $reporter->id,
        ]);

        $this->actingAs($satgas)
            ->get(route('satgas.hazards.show', $report))
            ->assertOk()
            ->assertSeeText('Pelindung mesin sudah dipasang kembali.')
            ->assertSeeText('mesin.jpg')
            ->assertSeeText('tidak memerlukan perubahan status lanjutan', false);
    }

    protected function createSatgasUser(): User
    {
        $role = Role::query()->create(['name' => 'Satgas', 'code' => 'satgas']);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    protected function createMahasiswaUser(): User
    {
        $role = Role::query()->create(['name' => 'Mahasiswa', 'code' => 'mahasiswa']);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    protected function createLocation(): Location
    {
        return Location::query()->create([
            'name' => 'Workshop Otomasi',
            'code' => 'WO-01',
            'is_active' => true,
        ]);
    }
}
