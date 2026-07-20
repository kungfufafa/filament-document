<?php

namespace Database\Factories;

use App\Enums\RecipientRole;
use App\Enums\RecipientStatus;
use App\Models\Document;
use App\Models\DocumentRecipient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DocumentRecipient>
 */
class DocumentRecipientFactory extends Factory
{
    protected $model = DocumentRecipient::class;

    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'role' => RecipientRole::Signer,
            'status' => RecipientStatus::Pending,
            'signing_order' => 1,
            'access_token' => (string) Str::uuid(),
        ];
    }
}
