<?php

namespace Database\Factories;

use App\Enums\ApprovalStatus;
use App\Enums\ApprovalType;
use App\Models\Approval;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Approval>
 */
class ApprovalFactory extends Factory
{
    protected $model = Approval::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'document_id' => null,
            'title' => fake()->sentence(3),
            'message' => fake()->optional()->paragraph(),
            'type' => ApprovalType::WithoutDocument,
            'status' => ApprovalStatus::Draft,
            'submitted_at' => null,
            'completed_at' => null,
        ];
    }
}
