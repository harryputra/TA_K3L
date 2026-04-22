<?php

namespace Tests\Unit;

use App\Models\IncidentReport;
use App\Models\Role;
use App\Models\User;
use App\Policies\IncidentReportPolicy;
use Tests\TestCase;

class IncidentReportPolicyTest extends TestCase
{
    public function test_reporter_can_view_their_own_incident_report(): void
    {
        $policy = new IncidentReportPolicy();
        $user = User::factory()->make(['id' => 10, 'is_active' => true]);
        $user->setRelation('role', new Role(['code' => 'mahasiswa']));

        $report = new IncidentReport([
            'reported_by' => 10,
            'status' => 'submitted',
        ]);

        $this->assertTrue($policy->view($user, $report));
    }

    public function test_regular_user_cannot_view_other_users_incident_report(): void
    {
        $policy = new IncidentReportPolicy();
        $user = User::factory()->make(['id' => 10, 'is_active' => true]);
        $user->setRelation('role', new Role(['code' => 'mahasiswa']));

        $report = new IncidentReport([
            'reported_by' => 11,
            'status' => 'submitted',
        ]);

        $this->assertFalse($policy->view($user, $report));
    }

    public function test_satgas_can_verify_submitted_incident_report(): void
    {
        $policy = new IncidentReportPolicy();
        $satgas = User::factory()->make(['id' => 20, 'is_active' => true]);
        $satgas->setRelation('role', new Role(['code' => 'satgas']));

        $report = new IncidentReport([
            'reported_by' => 10,
            'status' => 'submitted',
        ]);

        $this->assertTrue($policy->verify($satgas, $report));
    }

    public function test_satgas_cannot_verify_closed_incident_report(): void
    {
        $policy = new IncidentReportPolicy();
        $satgas = User::factory()->make(['id' => 20, 'is_active' => true]);
        $satgas->setRelation('role', new Role(['code' => 'satgas']));

        $report = new IncidentReport([
            'reported_by' => 10,
            'status' => 'closed',
        ]);

        $this->assertFalse($policy->verify($satgas, $report));
    }
}
