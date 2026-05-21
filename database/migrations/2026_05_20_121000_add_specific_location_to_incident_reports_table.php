<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->string('specific_location')->nullable()->after('location_accuracy');
        });

        foreach ($this->gisLocations() as $location) {
            DB::table('locations')->updateOrInsert(
                ['code' => $location['code']],
                [
                    'name' => $location['name'],
                    'description' => $location['description'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->dropColumn('specific_location');
        });
    }

    protected function gisLocations(): array
    {
        return [
            [
                'name' => 'Gedung Teori & Kantor',
                'code' => 'GIS-GEDUNG-TEORI',
                'description' => 'Area gedung berdasarkan polygon GIS kampus.',
            ],
            [
                'name' => 'Gedung Kantor',
                'code' => 'GIS-GEDUNG-KANTOR',
                'description' => 'Area gedung berdasarkan polygon GIS kampus.',
            ],
            [
                'name' => 'Gedung Mekanik',
                'code' => 'GIS-GEDUNG-MEKANIK',
                'description' => 'Area gedung berdasarkan polygon GIS kampus.',
            ],
            [
                'name' => 'Gedung FE',
                'code' => 'GIS-GEDUNG-FE',
                'description' => 'Area gedung berdasarkan polygon GIS kampus.',
            ],
            [
                'name' => 'Gedung GRC',
                'code' => 'GIS-GEDUNG-GRC',
                'description' => 'Area gedung berdasarkan polygon GIS kampus.',
            ],
            [
                'name' => 'Diluar Polman',
                'code' => 'GIS-DILUAR-POLMAN',
                'description' => 'Koordinat GPS berada di luar polygon area Polman yang terpetakan.',
            ],
        ];
    }
};
