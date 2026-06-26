<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seed DEMO — HANYA untuk mode demo/showcase (./run.sh tanpa argumen).
 *
 * Berisi:
 *  - akun contoh per-role (admin/satgas/mahasiswa), password: "password"
 *  - data insiden & potensi bahaya dummy yang realistis.
 *
 * DILARANG dijalankan di produksi (lihat `./run.sh deploy`). Idempoten.
 */
class DemoSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Pastikan role tersedia walau seeder ini dijalankan terpisah.
        $this->call(RoleSeeder::class);

        $roles = Role::query()->get()->keyBy('code');

        $accounts = [
            ['code' => 'admin', 'name' => 'Admin K3L', 'username' => 'admin.k3l', 'email' => 'admin@k3l.local', 'phone' => '081234567890'],
            ['code' => 'satgas', 'name' => 'Satgas K3L', 'username' => 'satgas.k3l', 'email' => 'satgas@k3l.local', 'phone' => '081234567891'],
            ['code' => 'mahasiswa', 'name' => 'Mahasiswa K3L', 'username' => 'mhs.k3l', 'email' => 'mahasiswa@k3l.local', 'phone' => '081234567892'],
        ];

        foreach ($accounts as $account) {
            User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'role_id' => $roles[$account['code']]->id ?? null,
                    'name' => $account['name'],
                    'username' => $account['username'],
                    'phone' => $account['phone'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ],
            );
        }

        // Data contoh insiden & hazard (termasuk titik GIS).
        $this->call(DummyIncidentsHazardsSeeder::class);
    }
}
