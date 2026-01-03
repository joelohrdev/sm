<?php

declare(strict_types=1);

use App\Models\Location;
use App\Models\Organization;

test('in array', function (): void {
    $location = Location::factory()->create()->refresh();

    expect(array_keys($location->toArray()))
        ->toBe([
            'id',
            'uuid',
            'organization_id',
            'name',
            'address',
            'maps_link',
            'created_at',
            'updated_at',
        ]);
});

test('belongs to organization', function (): void {
    $organization = Organization::factory()->create();
    $location = Location::factory()->for($organization)->create();

    expect($location->organization->is($organization))->toBeTrue();
});
