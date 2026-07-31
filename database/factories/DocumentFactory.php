<?php

namespace Database\Factories;

use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = Str::slug(fake()->words(3, true)).'.pdf';

        return [
            'user_id' => User::factory(),
            'folder_id' => null,
            'title' => fake()->sentence(3),
            'file_path' => 'documents/'.fake()->uuid().'/'.$filename,
            'original_filename' => $filename,
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(10_000, 5_000_000),
            'page_count' => fake()->numberBetween(1, 20),
            'status' => DocumentStatus::Draft,
            'document_type' => null,
            'sequential_signing' => false,
            'message' => null,
            'email_subject' => null,
            'sent_at' => null,
            'completed_at' => null,
            'expires_at' => null,
        ];
    }
}
