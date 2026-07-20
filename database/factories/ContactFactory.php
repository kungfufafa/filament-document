<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'job_title' => fake()->jobTitle(),
            'company' => fake()->company(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'visibility' => 'personal',
        ];
    }
}
