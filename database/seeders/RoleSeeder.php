<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Admin', 'code' => 'admin', 'description' => 'Pengelola sistem secara penuh.'],
            ['name' => 'Satgas', 'code' => 'satgas', 'description' => 'Petugas verifikasi dan tindak lanjut K3L.'],
            ['name' => 'Mahasiswa', 'code' => 'mahasiswa', 'description' => 'Pelapor insiden dan penerima informasi K3L.'],
        ] as $role) {
            Role::query()->updateOrCreate(
                ['code' => $role['code']],
                $role,
            );
        }
    }
}
