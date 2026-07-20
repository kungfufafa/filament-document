<?php

namespace Database\Factories;

use App\Models\EmeteraiBalance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmeteraiBalance>
 */
class EmeteraiBalanceFactory extends Factory
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
            'available' => fake()->numberBetween(0, 100),
            'used' => fake()->numberBetween(0, 50),
        ];
    }
}
