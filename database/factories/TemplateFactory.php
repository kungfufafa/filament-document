<?php

namespace Database\Factories;

use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Template>
 */
class TemplateFactory extends Factory
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
            'file_path' => 'templates/'.fake()->uuid().'/'.$filename,
            'original_filename' => $filename,
            'mime_type' => 'application/pdf',
            'page_count' => fake()->numberBetween(1, 20),
            'sequential_signing' => false,
            'recipient_roles' => null,
            'fields' => null,
            'status' => 'active',
        ];
    }
}
