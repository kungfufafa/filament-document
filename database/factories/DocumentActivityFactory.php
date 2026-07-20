<?php

namespace Database\Factories;

use App\Enums\DocumentActivityType;
use App\Models\Document;
use App\Models\DocumentActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentActivity>
 */
class DocumentActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'user_id' => User::factory(),
            'document_recipient_id' => null,
            'type' => DocumentActivityType::Created,
            'actor_name' => fake()->name(),
            'actor_email' => fake()->safeEmail(),
            'description' => fake()->sentence(),
            'meta' => null,
        ];
    }
}
