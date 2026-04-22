<?php

namespace Database\Seeders;

use App\Models\BodyPart;
use App\Models\IncidentCategory;
use App\Models\InjuryCategory;
use App\Models\Location;
use Illuminate\Database\Seeder;

class IncidentReferenceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Near Miss',
            'Unsafe Action',
            'Unsafe Condition',
            'Cedera Ringan',
            'Paparan Bahan Berbahaya',
        ] as $name) {
            IncidentCategory::query()->updateOrCreate(['name' => $name], ['description' => null]);
        }

        foreach ([
            'Tidak Ada Cedera',
            'Cedera Ringan',
            'Cedera Sedang',
            'Cedera Berat',
        ] as $name) {
            InjuryCategory::query()->updateOrCreate(['name' => $name], ['description' => null]);
        }

        foreach ([
            'Kepala',
            'Tangan',
            'Lengan',
            'Kaki',
            'Mata',
        ] as $name) {
            BodyPart::query()->updateOrCreate(['name' => $name], ['description' => null]);
        }

        foreach ([
            ['name' => 'Laboratorium Kimia', 'code' => 'LAB-KIM', 'description' => 'Area laboratorium praktikum kimia.'],
            ['name' => 'Workshop Teknik', 'code' => 'WORKSHOP', 'description' => 'Area kerja praktik dan peralatan teknik.'],
            ['name' => 'Gedung Perkuliahan A', 'code' => 'GPA', 'description' => 'Gedung kelas dan koridor akademik.'],
        ] as $location) {
            Location::query()->updateOrCreate(
                ['code' => $location['code']],
                [...$location, 'is_active' => true],
            );
        }
    }
}
