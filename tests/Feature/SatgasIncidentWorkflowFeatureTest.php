<?php

namespace Tests\Feature;

use App\Models\IncidentCategory;
use App\Models\IncidentReport;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SatgasIncidentWorkflowFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_satgas_can_update_incident_status_to_resolved(): void
    {
        $satgas = $this->createUserWithRole('satgas');
        $reporter = $this->createUserWithRole('mahasiswa');
        [$category, $location] = $this->incidentReferences();

        $report = IncidentReport::query()->create([
            'report_number' => 'INC-WF-001',
            'reported_by' => $reporter->id,
            'incident_category_id' => $category->id,
            'location_id' => $location->id,
            'incident_date' => '2026-04-25',
            'incident_time' => '08:00:00',
            'severity_level' => 'medium',
            'title' => 'Butuh tindakan lanjutan',
            'chronology' => 'Laporan sudah diverifikasi dan masuk proses penanganan.',
            'status' => 'investigating',
            'submitted_at' => now(),
        ]);

        $this->actingAs($satgas)
            ->patch(route('satgas.incidents.update-status', $report), [
                'status' => 'resolved',
                'status_note' => 'Perbaikan utama sudah dilakukan.',
            ])
            ->assertRedirect(route('satgas.incidents.show', $report))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('incident_reports', [
            'id' => $report->id,
            'status' => 'resolved',
        ]);

        $this->assertDatabaseHas('incident_status_histories', [
            'incident_report_id' => $report->id,
            'to_status' => 'resolved',
        ]);
    }

    public function test_satgas_can_add_follow_up_and_move_report_to_investigating(): void
    {
        $satgas = $this->createUserWithRole('satgas');
        $reporter = $this->createUserWithRole('mahasiswa');
        [$category, $location] = $this->incidentReferences();

        $report = IncidentReport::query()->create([
            'report_number' => 'INC-WF-002',
            'reported_by' => $reporter->id,
            'incident_category_id' => $category->id,
            'location_id' => $location->id,
            'incident_date' => '2026-04-25',
            'incident_time' => '09:00:00',
            'severity_level' => 'high',
            'title' => 'Perlu penjadwalan teknisi',
            'chronology' => 'Satgas akan mencatat tindak lanjut pertama.',
            'status' => 'verified',
            'submitted_at' => now(),
        ]);

        $this->actingAs($satgas)
            ->post(route('satgas.incidents.follow-ups.store', $report), [
                'action_taken' => 'Jadwalkan teknisi dan amankan area kerja sementara.',
                'action_owner_id' => $satgas->id,
                'due_date' => '2026-04-27',
                'status' => 'in_progress',
            ])
            ->assertRedirect(route('satgas.incidents.show', $report))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('incident_follow_ups', [
            'incident_report_id' => $report->id,
            'action_owner_id' => $satgas->id,
            'status' => 'in_progress',
        ]);

        $this->assertDatabaseHas('incident_reports', [
            'id' => $report->id,
            'status' => 'investigating',
        ]);
    }

    protected function createUserWithRole(string $roleCode): User
    {
        $role = Role::query()->firstOrCreate(
            ['code' => $roleCode],
            ['name' => ucfirst($roleCode)]
        );

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    protected function incidentReferences(): array
    {
        $category = IncidentCategory::query()->create(['name' => 'Unsafe Condition']);
        $location = Location::query()->create([
            'name' => 'Workshop Teknik',
            'code' => 'WORKSHOP',
            'is_active' => true,
        ]);

        return [$category, $location];
    }
}
