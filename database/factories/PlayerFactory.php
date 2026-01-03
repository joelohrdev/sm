<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Bats;
use App\Enums\Throws;
use App\Models\Organization;
use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Player>
 */
final class PlayerFactory extends Factory
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
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'dob' => $this->faker->date(),
            'graduation_year' => $this->faker->year(),
            'bats' => $this->faker->randomElement(Bats::class),
            'throws' => $this->faker->randomElement(Throws::class),
            'notes' => $this->faker->sentence(),
        ];
    }
}
