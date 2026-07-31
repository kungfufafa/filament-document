<?php

namespace Database\Factories;

use App\Enums\EmeteraiPurchaseStatus;
use App\Models\EmeteraiPurchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<EmeteraiPurchase>
 */
class EmeteraiPurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 50);
        $unitPrice = 10_000;

        return [
            'user_id' => User::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_amount' => $quantity * $unitPrice,
            'status' => EmeteraiPurchaseStatus::Completed,
            'transaction_number' => 'TRX-'.Str::upper(Str::random(10)),
        ];
    }
}
