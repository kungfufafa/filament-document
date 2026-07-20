<?php

namespace Database\Factories;

use App\Models\CorporateStamp;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CorporateStamp>
 */
class CorporateStampFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_name' => fake()->company(),
            'website' => fake()->optional()->url(),
            'logo_path' => null,
            'color' => fake()->hexColor(),
            'is_default' => false,
        ];
    }
}
