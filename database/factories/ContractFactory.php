<?php

namespace Database\Factories;

use App\Enums\ContractStatus;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contract>
 */
class ContractFactory extends Factory
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
            'document_id' => null,
            'title' => fake()->sentence(3),
            'counterparty' => fake()->optional()->company(),
            'contract_value' => fake()->optional()->randomFloat(2, 1_000_000, 500_000_000),
            'currency' => 'IDR',
            'effective_date' => fake()->optional()->date(),
            'expiry_date' => fake()->optional()->date(),
            'status' => ContractStatus::Draft,
            'notes' => null,
        ];
    }
}
