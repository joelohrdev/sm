<?php

declare(strict_types=1);

use App\Models\Guardian;
use App\Models\Organization;
use App\Models\User;

test('to array', function (): void {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
            'email',
            'email_verified_at',
            'created_at',
            'updated_at',
            'two_factor_confirmed_at',
        ]);
});

test('user has many organizations', function (): void {
    $user = User::factory()->create();
    Organization::factory()->count(3)->for($user)->create();

    expect($user->organizations)
        ->toHaveCount(3)
        ->each(fn ($organization) => $organization->user_id->toBe($user->id));
});

test('user has one guardian', function (): void {
    $user = User::factory()->create();
    $guardian = Guardian::factory()->for($user)->create();

    expect($user->guardian->is($guardian))->toBeTrue();
});

test('hidden attributes are not in array', function (): void {
    $user = User::factory()->create()->refresh();
    $array = $user->toArray();

    expect($array)->not->toHaveKeys(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes']);
});
