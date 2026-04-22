<?php

namespace Tests\Unit;

use App\Http\Requests\Admin\StoreIncidentCategoryRequest;
use App\Http\Requests\Admin\StoreLocationRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\PresenceVerifierInterface;
use Tests\TestCase;

class AdminMasterDataRequestTest extends TestCase
{
    public function test_location_request_validation_passes_for_valid_payload(): void
    {
        $request = new StoreLocationRequest();

        $validator = Validator::make([
            'name' => 'Laboratorium Fisika',
            'code' => 'LAB-FIS',
            'description' => 'Area praktikum fisika dasar.',
            'is_active' => true,
        ], $request->rules());

        $validator->setPresenceVerifier(new AdminMasterDataFakePresenceVerifier([
            'locations' => [],
            'locations_codes' => [],
        ]));

        $this->assertTrue($validator->passes());
    }

    public function test_incident_category_request_validation_fails_for_duplicate_name(): void
    {
        $request = new StoreIncidentCategoryRequest();

        $validator = Validator::make([
            'name' => 'Near Miss',
            'description' => 'Kategori duplikat',
        ], $request->rules());

        $validator->setPresenceVerifier(new AdminMasterDataFakePresenceVerifier([
            'incident_categories' => ['Near Miss'],
        ]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
    }
}

class AdminMasterDataFakePresenceVerifier implements PresenceVerifierInterface
{
    public function __construct(
        protected array $existingValues = [],
    ) {
    }

    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = []): int
    {
        $bucket = $this->existingValues[$collection] ?? [];

        return in_array($value, $bucket, true) ? 1 : 0;
    }

    public function getMultiCount($collection, $column, array $values, array $extra = []): int
    {
        return count(array_intersect($values, $this->existingValues[$collection] ?? []));
    }

    public function setConnection($connection): void
    {
        // No real database connection is needed in this validator stub.
    }
}
