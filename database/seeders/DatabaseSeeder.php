<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seed default (`php artisan db:seed` / `migrate --seed`).
 *
 * = ESENSIAL + DEMO (seed penuh untuk dev/CI).
 *
 * Untuk produksi gunakan HANYA EssentialSeeder lewat `./run.sh deploy`
 * (atau `php artisan db:seed --class=EssentialSeeder`), JANGAN seeder ini.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            EssentialSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
