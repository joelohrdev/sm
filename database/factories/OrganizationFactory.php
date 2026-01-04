<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organization>
 */
final class OrganizationFactory extends Factory
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
            'name' => $this->faker->company(),
            'slug' => $this->faker->slug(),
            'owner_id' => User::factory(),
            'logo_path' => $this->faker->imageUrl(),
            'primary_color' => $this->faker->hexColor(),
        ];
    }
}
