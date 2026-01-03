<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\Season;
use App\Models\Team;

test('to array', function (): void {
    $season = Season::factory()->create()->refresh();

    expect(array_keys($season->toArray()))
        ->toBe([
            'id',
            'uuid',
            'organization_id',
            'name',
            'start_date',
            'end_date',
            'active',
            'is_registration_open',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);
});

test('belongs to organization', function (): void {
    $organization = Organization::factory()->create();
    $season = Season::factory()->for($organization)->create();

    expect($season->organization->is($organization))->toBeTrue();
});

test('season has many teams', function (): void {
    $season = Season::factory()->create();
    Team::factory()->count(3)->for($season)->create();

    expect($season->teams)
        ->toHaveCount(3)
        ->each(fn ($team) => $team->season_id->toBe($season->id));
});
