<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class UserDashboardRouteTest extends TestCase
{
    public function test_admin_user_gets_admin_dashboard_route_name(): void
    {
        $user = User::factory()->make();
        $user->setRelation('role', new Role(['code' => 'admin']));

        $this->assertSame('admin.dashboard', $user->dashboardRouteName());
    }

    public function test_satgas_user_gets_satgas_dashboard_route_name(): void
    {
        $user = User::factory()->make();
        $user->setRelation('role', new Role(['code' => 'satgas']));

        $this->assertSame('satgas.dashboard', $user->dashboardRouteName());
    }

    public function test_default_user_gets_user_dashboard_route_name(): void
    {
        $user = User::factory()->make();
        $user->setRelation('role', new Role(['code' => 'mahasiswa']));

        $this->assertSame('user.dashboard', $user->dashboardRouteName());
    }
}
