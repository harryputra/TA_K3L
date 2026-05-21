<?php

namespace Tests\Unit;

use App\Http\Requests\Incident\StoreIncidentReportRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\PresenceVerifierInterface;
use Tests\TestCase;

class StoreIncidentReportRequestTest extends TestCase
{
    public function test_validation_passes_for_a_complete_incident_report_payload(): void
    {
        $request = new StoreIncidentReportRequest();

        $validator = Validator::make([
            'title' => 'Tumpahan bahan kimia ringan di meja praktikum',
            'incident_category_id' => 1,
            'location_id' => 10,
            'latitude' => '-6.8761000',
            'longitude' => '107.6206300',
            'location_accuracy' => '8.50',
            'specific_location' => 'Lantai 2 dekat panel utama',
            'injuries' => [
                [
                    'injury_category_id' => 2,
                    'body_part_id' => 3,
                    'description' => 'Lecet ringan',
                ],
                [
                    'injury_category_id' => 3,
                    'body_part_id' => 4,
                    'description' => 'Memar',
                ],
            ],
            'incident_date' => now()->toDateString(),
            'incident_time' => '09:30',
            'severity_level' => 'medium',
            'victim_type' => 'self',
            'victim_user_id' => 5,
            'chronology' => 'Saat praktikum berlangsung, botol kecil bahan kimia tersenggol dan cairan tumpah ke meja. Area segera diamankan dan dosen diberi tahu.',
            'cause' => 'Penempatan botol terlalu dekat dengan area gerak mahasiswa.',
            'initial_action' => 'Meja dibersihkan dan area diberi tanda peringatan.',
            'impact' => 'Tidak ada cedera, tetapi aktivitas praktikum sempat dihentikan sementara.',
        ], $request->rules(), $request->messages());

        $validator->setPresenceVerifier(new FakePresenceVerifier([
            'incident_categories' => [1],
            'injury_categories' => [2, 3],
            'body_parts' => [3, 4],
            'locations' => [10],
            'users' => [5],
        ]));

        $this->assertTrue($validator->passes());
    }

    public function test_validation_fails_for_invalid_gps_coordinates(): void
    {
        $request = new StoreIncidentReportRequest();

        $validator = Validator::make([
            'title' => 'Tumpahan bahan kimia ringan di meja praktikum',
            'incident_category_id' => 1,
            'location_id' => 10,
            'latitude' => '-91',
            'longitude' => '181',
            'location_accuracy' => '-1',
            'incident_date' => now()->toDateString(),
            'chronology' => 'Saat praktikum berlangsung, area kejadian diamankan dan dosen diberi tahu oleh mahasiswa.',
        ], $request->rules(), $request->messages());

        $validator->setPresenceVerifier(new FakePresenceVerifier([
            'incident_categories' => [1],
            'locations' => [10],
        ]));

        $this->assertTrue($validator->fails());
        $this->assertSame([
            'latitude',
            'longitude',
            'location_accuracy',
        ], array_keys($validator->errors()->messages()));
    }

    public function test_validation_fails_for_an_invalid_incident_report_payload(): void
    {
        $request = new StoreIncidentReportRequest();

        $validator = Validator::make([
            'title' => '',
            'incident_category_id' => 999,
            'location_id' => 777,
            'incident_date' => now()->addDay()->toDateString(),
            'severity_level' => 'urgent',
            'victim_type' => 'unknown',
            'chronology' => 'Terlalu singkat',
        ], $request->rules(), $request->messages());

        $validator->setPresenceVerifier(new FakePresenceVerifier([
            'incident_categories' => [1],
            'locations' => [10],
        ]));

        $this->assertTrue($validator->fails());
        $this->assertSame([
            'title',
            'incident_category_id',
            'location_id',
            'incident_date',
            'severity_level',
            'victim_type',
            'chronology',
        ], array_keys($validator->errors()->messages()));
    }
}

class FakePresenceVerifier implements PresenceVerifierInterface
{
    public function __construct(
        protected array $existingValues = [],
    ) {
    }

    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = []): int
    {
        return in_array((int) $value, $this->existingValues[$collection] ?? [], true) ? 1 : 0;
    }

    public function getMultiCount($collection, $column, array $values, array $extra = []): int
    {
        return count(array_intersect($values, $this->existingValues[$collection] ?? []));
    }

    public function setConnection($connection): void
    {
        // Validation in this test uses an in-memory verifier stub, so no connection is needed.
    }
}
