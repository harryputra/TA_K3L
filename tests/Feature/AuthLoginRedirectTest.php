<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_satgas_login_redirects_to_satgas_dashboard_even_with_user_intended_url(): void
    {
        $satgasRole = Role::query()->create([
            'name' => 'Satgas',
            'code' => 'satgas',
        ]);

        $satgas = User::factory()->create([
            'role_id' => $satgasRole->id,
            'password' => 'password',
            'is_active' => true,
        ]);

        $this->withSession([
            'url.intended' => route('user.dashboard'),
        ])->post(route('login.attempt'), [
            'login' => $satgas->email,
            'password' => 'password',
        ])->assertRedirect(route('satgas.dashboard'));
    }
}
