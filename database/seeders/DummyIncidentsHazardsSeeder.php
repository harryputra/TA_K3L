<?php

namespace Database\Seeders;

use App\Models\IncidentReport;
use App\Models\PotentialHazardReport;
use App\Models\CampusRoom;
use App\Models\IncidentCategory;
use App\Models\InjuryCategory;
use App\Models\Location;
use Illuminate\Database\Seeder;

class DummyIncidentsHazardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get campus rooms for floors 2 and 3 (6 rooms each)
        $floor2Rooms = CampusRoom::query()
            ->where('building_key', 'gedung-teori')
            ->where('floor', 2)
            ->where('is_active', true)
            ->limit(6)
            ->get();

        $floor3Rooms = CampusRoom::query()
            ->where('building_key', 'gedung-teori')
            ->where('floor', 3)
            ->where('is_active', true)
            ->limit(6)
            ->get();

        // Get or create incident category
        $incidentCategory = IncidentCategory::query()
            ->firstOrCreate(['name' => 'Keselamatan Umum']);

        // Get or create injury category
        $injuryCategory = InjuryCategory::query()
            ->firstOrCreate(['name' => 'Luka Ringan']);
        
        // Get a default location
        $location = Location::query()->first();
        if (!$location) {
            $location = Location::query()->create([
                'name' => 'Kampus Polman',
                'is_active' => true,
            ]);
        }
        $locationId = $location->id;

        // Koordinat untuk gedung teori (area polygon gedung teori)
        // Semua koordinat ini berada di dalam polygon Gedung Teori & Kantor
        $roomCoordinates = [
            // Lantai 2
            [
                'latitude' => -6.87740,
                'longitude' => 107.62050,
            ],
            [
                'latitude' => -6.87742,
                'longitude' => 107.62070,
            ],
            [
                'latitude' => -6.87744,
                'longitude' => 107.62090,
            ],
            [
                'latitude' => -6.87746,
                'longitude' => 107.62100,
            ],
            [
                'latitude' => -6.87748,
                'longitude' => 107.62080,
            ],
            [
                'latitude' => -6.87750,
                'longitude' => 107.62060,
            ],
            // Lantai 3 (koordinat sedikit berbeda)
            [
                'latitude' => -6.87738,
                'longitude' => 107.62055,
            ],
            [
                'latitude' => -6.87740,
                'longitude' => 107.62075,
            ],
            [
                'latitude' => -6.87742,
                'longitude' => 107.62095,
            ],
            [
                'latitude' => -6.87744,
                'longitude' => 107.62105,
            ],
            [
                'latitude' => -6.87746,
                'longitude' => 107.62085,
            ],
            [
                'latitude' => -6.87748,
                'longitude' => 107.62065,
            ],
        ];

        $riskLevels = ['rendah', 'sedang', 'tinggi', 'kritis'];
        $severityLevels = ['low', 'medium', 'high', 'critical'];
        $hazardTypes = ['lingkungan', 'peralatan', 'listrik', 'zat-kimia'];
        $genders = ['male', 'female'];
        $incidentStatuses = ['submitted', 'verified', 'investigating', 'resolved'];
        $hazardStatuses = ['submitted', 'reviewed', 'resolved'];

        $incidentTitles = [
            'Jatuh dari kursi',
            'Terpeleset lantai basah',
            'Tersengat listrik',
            'Terkena pecahan kaca',
            'Luka tergores meja',
            'Terjepit pintu',
            'Memar karena benturan',
            'Luka bakar terkena alat'
        ];

        $hazardTitles = [
            'Lantai licin',
            'Kabel terbuka',
            'Penerangan kurang',
            'Rak tidak stabil',
            'Stop kontak rusak',
            'Peralatan tidak terawat',
            'Ventilasi buruk',
            'Penempatan barang berbahaya'
        ];

        $chronologies = [
            'Insiden terjadi saat korban sedang berjalan di dalam ruangan.',
            'Korban terjatuh saat akan duduk di kursi yang tidak stabil.',
            'Insiden terjadi saat praktikum berlangsung.',
            'Korban tidak sengaja menyentuh peralatan yang panas.',
        ];

        $hazardNotes = [
            'Potensi bahaya perlu segera ditindaklanjuti.',
            'Dapat menyebabkan cedera serius jika tidak segera diperbaiki.',
            'Telah dilaporkan ke petugas kebersihan/teknis.',
            'Perlu dilakukan perbaikan segera.',
        ];

        // Helper function to generate unique report number
        $generateReportNumber = function ($prefix, $floor, $roomIndex, $seq) {
            return $prefix . '-F' . $floor . '-R' . sprintf('%02d', $roomIndex + 1) . '-' . sprintf('%03d', $seq) . '-' . uniqid();
        };

        // ==================== SEED INCIDENTS ====================
        // Floor 2 - 6 rooms, each with 3 incidents
        foreach ($floor2Rooms as $roomIndex => $room) {
            for ($i = 0; $i < 3; $i++) {
                $incidentNumber = $i + 1;
                $coords = $roomCoordinates[$roomIndex];
                
                IncidentReport::query()->create([
                    'report_number' => $generateReportNumber('INC', 2, $roomIndex, $incidentNumber),
                    'reported_by' => 3,
                    'reporter_name' => 'Pelapor Lantai 2 - Ruang ' . ($roomIndex + 1),
                    'reporter_email' => 'pelapor.f2.r' . ($roomIndex + 1) . '@student.polman.ac.id',
                    'reporter_whatsapp' => '6281234567' . sprintf('%02d', $roomIndex * 3 + $i + 1),
                    'victim_name' => 'Korban Insiden L2 R' . ($roomIndex + 1) . '#' . $incidentNumber,
                    'victim_position' => 'Mahasiswa',
                    'victim_gender' => $genders[$i % 2],
                    'incident_category_id' => $incidentCategory->id,
                    'injury_category_id' => $injuryCategory->id,
                    'location_id' => $locationId,
                    'campus_room_id' => $room->id,
                    'building_key' => 'gedung-teori',
                    'building_floor' => 2,
                    'specific_location' => $room->name ?? 'Ruang ' . ($roomIndex + 1),
                    'latitude' => $coords['latitude'],
                    'longitude' => $coords['longitude'],
                    'location_accuracy' => 10.00,
                    'incident_date' => now()->subDays(rand(1, 30))->toDateString(),
                    'incident_time' => sprintf('09:%02d', rand(0, 59)),
                    'witness_name' => 'Saksi L2 R' . ($roomIndex + 1) . '#' . $incidentNumber,
                    'severity_level' => $severityLevels[$i % 4],
                    'title' => $incidentTitles[($roomIndex + $i) % count($incidentTitles)],
                    'chronology' => $chronologies[$i % count($chronologies)],
                    'cause' => 'Faktor lingkungan atau perilaku tidak aman.',
                    'initial_action' => 'Pertolongan pertama diberikan oleh petugas K3L.',
                    'impact' => 'Luka ringan pada korban, sudah mendapat perawatan.',
                    'status' => $incidentStatuses[$i % 4],
                    'submitted_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        // Floor 3 - 6 rooms, each with 3 incidents
        foreach ($floor3Rooms as $roomIndex => $room) {
            for ($i = 0; $i < 3; $i++) {
                $incidentNumber = $i + 1;
                $coords = $roomCoordinates[$roomIndex + 6];
                
                IncidentReport::query()->create([
                    'report_number' => $generateReportNumber('INC', 3, $roomIndex, $incidentNumber),
                    'reported_by' => 3,
                    'reporter_name' => 'Pelapor Lantai 3 - Ruang ' . ($roomIndex + 1),
                    'reporter_email' => 'pelapor.f3.r' . ($roomIndex + 1) . '@student.polman.ac.id',
                    'reporter_whatsapp' => '6281234567' . sprintf('%02d', 20 + $roomIndex * 3 + $i + 1),
                    'victim_name' => 'Korban Insiden L3 R' . ($roomIndex + 1) . '#' . $incidentNumber,
                    'victim_position' => 'Mahasiswa',
                    'victim_gender' => $genders[($i + 1) % 2],
                    'incident_category_id' => $incidentCategory->id,
                    'injury_category_id' => $injuryCategory->id,
                    'location_id' => $locationId,
                    'campus_room_id' => $room->id,
                    'building_key' => 'gedung-teori',
                    'building_floor' => 3,
                    'specific_location' => $room->name ?? 'Ruang ' . ($roomIndex + 1),
                    'latitude' => $coords['latitude'],
                    'longitude' => $coords['longitude'],
                    'location_accuracy' => 10.00,
                    'incident_date' => now()->subDays(rand(1, 30))->toDateString(),
                    'incident_time' => sprintf('10:%02d', rand(0, 59)),
                    'witness_name' => 'Saksi L3 R' . ($roomIndex + 1) . '#' . $incidentNumber,
                    'severity_level' => $severityLevels[($i + 2) % 4],
                    'title' => $incidentTitles[($roomIndex + $i + 3) % count($incidentTitles)],
                    'chronology' => $chronologies[($i + 1) % count($chronologies)],
                    'cause' => 'Kelalaian atau kondisi peralatan yang kurang baik.',
                    'initial_action' => 'Evakuasi dan pertolongan pertama segera diberikan.',
                    'impact' => 'Korban mengalami luka dan telah dibawa ke UKS.',
                    'status' => $incidentStatuses[($i + 1) % 4],
                    'submitted_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        // ==================== SEED HAZARDS ====================
        // Floor 2 - 6 rooms, each with 3 hazards
        foreach ($floor2Rooms as $roomIndex => $room) {
            for ($i = 0; $i < 3; $i++) {
                $hazardNumber = $i + 1;
                $coords = $roomCoordinates[$roomIndex];
                
                PotentialHazardReport::query()->create([
                    'report_number' => $generateReportNumber('HAZ', 2, $roomIndex, $hazardNumber),
                    'reported_by' => 3,
                    'reporter_name' => 'Pelapor Hazard L2 R' . ($roomIndex + 1),
                    'reporter_email' => 'hazard.f2.r' . ($roomIndex + 1) . '@polman.ac.id',
                    'reporter_whatsapp' => '6281234567' . sprintf('%02d', 40 + $roomIndex * 3 + $i + 1),
                    'location_id' => $locationId,
                    'campus_room_id' => $room->id,
                    'building_key' => 'gedung-teori',
                    'building_floor' => 2,
                    'hazard_type' => $hazardTypes[($roomIndex + $i) % 4],
                    'title' => $hazardTitles[($roomIndex + $i) % count($hazardTitles)],
                    'specific_location' => $room->name ?? 'Ruang ' . ($roomIndex + 1),
                    'risk_level' => $riskLevels[$i % 4],
                    'latitude' => $coords['latitude'],
                    'longitude' => $coords['longitude'],
                    'location_accuracy' => 10.00,
                    'floorplan_x' => rand(100, 1500),
                    'floorplan_y' => rand(100, 1000),
                    'notes' => $hazardNotes[$i % count($hazardNotes)],
                    'status' => $hazardStatuses[$i % 3],
                    'submitted_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        // Floor 3 - 6 rooms, each with 3 hazards
        foreach ($floor3Rooms as $roomIndex => $room) {
            for ($i = 0; $i < 3; $i++) {
                $hazardNumber = $i + 1;
                $coords = $roomCoordinates[$roomIndex + 6];
                
                PotentialHazardReport::query()->create([
                    'report_number' => $generateReportNumber('HAZ', 3, $roomIndex, $hazardNumber),
                    'reported_by' => 3,
                    'reporter_name' => 'Pelapor Hazard L3 R' . ($roomIndex + 1),
                    'reporter_email' => 'hazard.f3.r' . ($roomIndex + 1) . '@polman.ac.id',
                    'reporter_whatsapp' => '6281234567' . sprintf('%02d', 60 + $roomIndex * 3 + $i + 1),
                    'location_id' => $locationId,
                    'campus_room_id' => $room->id,
                    'building_key' => 'gedung-teori',
                    'building_floor' => 3,
                    'hazard_type' => $hazardTypes[($roomIndex + $i + 2) % 4],
                    'title' => $hazardTitles[($roomIndex + $i + 2) % count($hazardTitles)],
                    'specific_location' => $room->name ?? 'Ruang ' . ($roomIndex + 1),
                    'risk_level' => $riskLevels[($i + 1) % 4],
                    'latitude' => $coords['latitude'],
                    'longitude' => $coords['longitude'],
                    'location_accuracy' => 10.00,
                    'floorplan_x' => rand(100, 1500),
                    'floorplan_y' => rand(100, 1000),
                    'notes' => $hazardNotes[($i + 1) % count($hazardNotes)],
                    'status' => $hazardStatuses[($i + 1) % 3],
                    'submitted_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        // Summary output
        $totalIncidents = (count($floor2Rooms) + count($floor3Rooms)) * 3;
        $totalHazards = (count($floor2Rooms) + count($floor3Rooms)) * 3;
        
        $this->command->info("✅ Dummy data seeded successfully!");
        $this->command->info("📊 Summary:");
        $this->command->info("   - Rooms: " . (count($floor2Rooms) + count($floor3Rooms)) . " rooms");
        $this->command->info("   - Incidents: {$totalIncidents} incidents (3 per room)");
        $this->command->info("   - Hazards: {$totalHazards} hazards (3 per room)");
        $this->command->info("   - All records have latitude & longitude coordinates inside Gedung Teori polygon!");
    }
}