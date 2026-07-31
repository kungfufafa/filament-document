<?php

namespace Database\Factories;

use App\Enums\FieldType;
use App\Models\Document;
use App\Models\DocumentField;
use App\Models\DocumentRecipient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentField>
 */
class DocumentFieldFactory extends Factory
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
            'document_recipient_id' => fn (array $attributes): int => DocumentRecipient::factory()->create([
                'document_id' => $attributes['document_id'],
            ])->id,
            'type' => FieldType::Signature,
            'page' => 1,
            'x' => fake()->randomFloat(4, 0, 80),
            'y' => fake()->randomFloat(4, 0, 90),
            'width' => 20,
            'height' => 8,
            'required' => true,
            'value' => null,
            'filled_at' => null,
        ];
    }
}
