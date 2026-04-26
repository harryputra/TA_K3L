<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            IncidentReferenceSeeder::class,
            KnowledgeSeeder::class,
            EmergencyCenterSeeder::class,
        ]);

        $roles = Role::query()->get()->keyBy('code');

        User::query()->updateOrCreate(
            ['email' => 'admin@k3l.local'],
            [
                'role_id' => $roles['admin']->id ?? null,
                'name' => 'Admin K3L',
                'username' => 'admin.k3l',
                'phone' => '081234567890',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'satgas@k3l.local'],
            [
                'role_id' => $roles['satgas']->id ?? null,
                'name' => 'Satgas K3L',
                'username' => 'satgas.k3l',
                'phone' => '081234567891',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'mahasiswa@k3l.local'],
            [
                'role_id' => $roles['mahasiswa']->id ?? null,
                'name' => 'Mahasiswa K3L',
                'username' => 'mhs.k3l',
                'phone' => '081234567892',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
        );
    }
}
