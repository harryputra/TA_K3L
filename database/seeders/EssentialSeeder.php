<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seed ESENSIAL — AMAN untuk produksi.
 *
 * Hanya berisi:
 *  - data referensi/lookup (role, kategori insiden, knowledge, emergency center)
 *  - 1 akun admin asli yang diambil dari .env (ADMIN_EMAIL / ADMIN_PASSWORD).
 *
 * TIDAK pernah membuat data contoh/dummy. Idempoten (aman diulang).
 * Dipanggil oleh `./run.sh deploy` dan juga `./run.sh` (demo).
 */
class EssentialSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Role harus ada lebih dulu agar admin bisa di-assign.
        $this->call(RoleSeeder::class);

        $adminRoleId = Role::query()->where('code', 'admin')->value('id');
        $adminEmail = env('ADMIN_EMAIL', 'admin@k3l.local');
        $adminUsername = Str::slug(Str::before($adminEmail, '@'), '.') ?: 'admin';

        User::query()->updateOrCreate(
            ['email' => $adminEmail],
            [
                'role_id' => $adminRoleId,
                'name' => env('ADMIN_NAME', 'Admin K3L'),
                'username' => $adminUsername,
                'phone' => env('ADMIN_PHONE', '081234567890'),
                'email_verified_at' => now(),
                'password' => Hash::make((string) env('ADMIN_PASSWORD', 'password')),
                'is_active' => true,
            ],
        );

        // Data referensi/lookup + konten resmi — aman dijalankan di produksi.
        // KnowledgeSeeder mencari akun admin sebagai author, jadi dijalankan
        // setelah admin di atas dibuat.
        $this->call([
            IncidentReferenceSeeder::class,
            KnowledgeSeeder::class,
            EmergencyCenterSeeder::class,
        ]);
    }
}
