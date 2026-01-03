<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\Player;

test('in array', function (): void {
    $player = Player::factory()->create()->refresh();

    expect(array_keys($player->toArray()))
        ->toBe([
            'id',
            'uuid',
            'organization_id',
            'first_name',
            'last_name',
            'dob',
            'graduation_year',
            'bats',
            'throws',
            'notes',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);
});

test('belongs to organization', function (): void {
    $organization = Organization::factory()->create();
    $player = Player::factory()->for($organization)->create();

    expect($player->organization->is($organization))->toBeTrue();
});
