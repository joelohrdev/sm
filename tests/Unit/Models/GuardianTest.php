<?php

declare(strict_types=1);

use App\Models\Guardian;
use App\Models\Organization;
use App\Models\Player;
use App\Models\User;

test('in array', function (): void {
    $guardian = Guardian::factory()->create()->refresh();

    expect(array_keys($guardian->toArray()))
        ->toBe([
            'id',
            'uuid',
            'organization_id',
            'user_id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'created_at',
            'updated_at',
        ]);
});

test('belongs to organization', function (): void {
    $organization = Organization::factory()->create();
    $guardian = Guardian::factory()->for($organization)->create();

    expect($guardian->organization->is($organization))->toBeTrue();
});

test('belongs to user', function (): void {
    $user = User::factory()->create();
    $guardian = Guardian::factory()->for($user)->create();

    expect($guardian->user->is($user))->toBeTrue();
});

test('belongs to many players', function (): void {
    $guardian = Guardian::factory()->create();
    $players = Player::factory()->count(3)->create();

    $guardian->players()->attach($players, ['relationship' => 'Parent']);

    expect($guardian->players)
        ->toHaveCount(3)
        ->each(fn ($player) => $player->toBeInstanceOf(Player::class));

    $players->each(function ($player) use ($guardian): void {
        expect($player->guardians)
            ->toHaveCount(1)
            ->first()->id->toBe($guardian->id);
    });
});
