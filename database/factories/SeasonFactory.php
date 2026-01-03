<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Season>
 */
final class SeasonFactory extends Factory
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
            'name' => $this->faker->year(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'active' => $this->faker->boolean(),
            'is_registration_open' => $this->faker->boolean(),
        ];
    }
}
