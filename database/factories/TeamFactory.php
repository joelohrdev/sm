<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Division;
use App\Models\Organization;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
final class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'organization_id' => Organization::factory(),
            'season_id' => Season::factory(),
            'division_id' => Division::factory(),
            'name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'head_coach_id' => User::factory(),
        ];
    }
}
