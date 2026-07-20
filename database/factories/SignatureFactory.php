<?php

namespace Database\Factories;

use App\Enums\SignatureMethod;
use App\Models\Signature;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Signature>
 */
class SignatureFactory extends Factory
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
            'method' => SignatureMethod::Draw,
            'fullname' => fake()->name(),
            'initials' => strtoupper(fake()->lexify('??')),
            'image_path' => null,
            'draw_data' => null,
            'is_default' => false,
        ];
    }
}
