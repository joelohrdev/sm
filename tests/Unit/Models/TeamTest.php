<?php

declare(strict_types=1);

use App\Models\Division;
use App\Models\Organization;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;

test('in array', function (): void {
    $team = Team::factory()->create()->refresh();

    expect(array_keys($team->toArray()))
        ->toBe([
            'id',
            'uuid',
            'organization_id',
            'season_id',
            'division_id',
            'name',
            'slug',
            'head_coach_id',
            'created_at',
            'updated_at',
        ]);
});

test('belongs to organization', function (): void {
    $organization = Organization::factory()->create();
    $team = Team::factory()->for($organization)->create();

    expect($team->organization->is($organization))->toBeTrue();
});

test('belongs to season', function (): void {
    $season = Season::factory()->create();
    $team = Team::factory()->for($season)->create();

    expect($team->season->is($season))->toBeTrue();
});

test('belongs to division', function (): void {
    $division = Division::factory()->create();
    $team = Team::factory()->for($division)->create();

    expect($team->division->is($division))->toBeTrue();
});

test('belongs to head coach', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->for($user, 'headCoach')->create();

    expect($team->headCoach->is($user))->toBeTrue();
});
