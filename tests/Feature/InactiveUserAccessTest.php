<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Support\Dashboard\SatgasDashboardData;
use App\Support\Dashboard\UserDashboardData;
use Tests\TestCase;

class InactiveUserAccessTest extends TestCase
{
    public function test_inactive_mahasiswa_is_redirected_to_login_when_accessing_user_dashboard(): void
    {
        $this->instance(UserDashboardData::class, new class
        {
            public function build(int $userId): array
            {
                return [
                    'stats' => [
                        'my_reports' => 0,
                        'submitted_reports' => 0,
                        'verified_reports' => 0,
                        'closed_reports' => 0,
                    ],
                    'recentReports' => collect(),
                    'publishedKnowledgeCount' => 0,
                ];
            }
        });

        $role = new Role([
            'name' => 'Mahasiswa',
            'code' => 'mahasiswa',
        ]);

        $user = User::factory()->make([
            'id' => 301,
            'is_active' => false,
        ]);
        $user->setRelation('role', $role);

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('login');
    }

    public function test_inactive_satgas_is_redirected_to_login_when_accessing_satgas_dashboard(): void
    {
        $this->instance(SatgasDashboardData::class, new class
        {
            public function build(): array
            {
                return [
                    'stats' => [
                        'submitted_incidents' => 0,
                        'verified_incidents' => 0,
                        'investigating_incidents' => 0,
                        'critical_incidents' => 0,
                    ],
                    'priorityReports' => collect(),
                ];
            }
        });

        $role = new Role([
            'name' => 'Satgas',
            'code' => 'satgas',
        ]);

        $user = User::factory()->make([
            'id' => 302,
            'is_active' => false,
        ]);
        $user->setRelation('role', $role);

        $this->actingAs($user)
            ->get(route('satgas.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('login');
    }
}
