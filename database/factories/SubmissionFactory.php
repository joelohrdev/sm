<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Form;
use App\Models\Player;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
final class SubmissionFactory extends Factory
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
            'form_id' => Form::factory(),
            'user_id' => User::factory(),
            'player_id' => Player::factory(),
            'data' => [
                'first_name' => $this->faker->firstName(),
                'last_name' => $this->faker->lastName(),
                'email' => $this->faker->unique()->safeEmail(),
                'phone' => $this->faker->phoneNumber(),
                'date_of_birth' => $this->faker->date(),
                'address' => [
                    'street' => $this->faker->streetAddress(),
                    'city' => $this->faker->city(),
                    'state' => $this->faker->randomElement(['AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY']),
                    'zip' => $this->faker->postcode(),
                ],
                'emergency_contact' => [
                    'name' => $this->faker->name(),
                    'relationship' => $this->faker->randomElement(['Parent', 'Guardian', 'Spouse', 'Sibling', 'Other']),
                    'phone' => $this->faker->phoneNumber(),
                ],
                'medical_conditions' => $this->faker->optional()->sentence(),
                'allergies' => $this->faker->optional()->words(3, true),
                'shirt_size' => $this->faker->randomElement(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
                'agree_to_terms' => true,
                'additional_notes' => $this->faker->optional()->paragraph(),
            ],
            'payment_status' => $this->faker->randomElement(PaymentStatus::cases()),
        ];
    }
}
