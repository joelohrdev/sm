<?php

declare(strict_types=1);

use App\Models\Division;
use App\Models\Form;
use App\Models\Guardian;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Player;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;

test('to array', function (): void {
    $organization = Organization::factory()->create()->refresh();

    expect(array_keys($organization->toArray()))
        ->toBe([
            'id',
            'uuid',
            'name',
            'slug',
            'owner_id',
            'logo_path',
            'primary_color',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);
});

test('belongs to an owner', function (): void {
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user, 'owner')->create();

    expect($organization->owner->is($user))->toBeTrue();
});

test('has many seasons', function (): void {
    $organization = Organization::factory()->create();
    Season::factory()->count(3)->for($organization)->create();

    expect($organization->seasons)
        ->toHaveCount(3)
        ->each(fn ($season) => $season->organization_id->toBe($organization->id));
});

test('has many divisions', function (): void {
    $organization = Organization::factory()->create();
    Division::factory()->count(3)->for($organization)->create();

    expect($organization->divisions)
        ->toHaveCount(3)
        ->each(fn ($division) => $division->organization_id->toBe($organization->id));
});

test('has many locations', function (): void {
    $organization = Organization::factory()->create();
    Location::factory()->count(3)->for($organization)->create();

    expect($organization->locations)
        ->toHaveCount(3)
        ->each(fn ($location) => $location->organization_id->toBe($organization->id));
});

test('has many players', function (): void {
    $organization = Organization::factory()->create();
    Player::factory()->count(3)->for($organization)->create();

    expect($organization->players)
        ->toHaveCount(3)
        ->each(fn ($player) => $player->organization_id->toBe($organization->id));
});

test('has many guardians', function (): void {
    $organization = Organization::factory()->create();
    Guardian::factory()->count(3)->for($organization)->create();

    expect($organization->guardians)
        ->toHaveCount(3)
        ->each(fn ($guardian) => $guardian->organization_id->toBe($organization->id));
});

test('has many teams', function (): void {
    $organization = Organization::factory()->create();
    Team::factory()->count(3)->for($organization)->create();

    expect($organization->teams)
        ->toHaveCount(3)
        ->each(fn ($team) => $team->organization_id->toBe($organization->id));
});

test('has many forms', function (): void {
    $organization = Organization::factory()->create();
    Form::factory()->count(3)->for($organization)->create();

    expect($organization->forms)
        ->toHaveCount(3)
        ->each(fn ($form) => $form->organization_id->toBe($organization->id));
});
