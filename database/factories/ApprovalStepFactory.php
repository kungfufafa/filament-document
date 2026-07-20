<?php

namespace Database\Factories;

use App\Enums\ApprovalStepStatus;
use App\Models\Approval;
use App\Models\ApprovalStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApprovalStep>
 */
class ApprovalStepFactory extends Factory
{
    protected $model = ApprovalStep::class;

    public function definition(): array
    {
        return [
            'approval_id' => Approval::factory(),
            'approver_name' => fake()->name(),
            'approver_email' => fake()->safeEmail(),
            'step_order' => 1,
            'status' => ApprovalStepStatus::Pending,
            'notes' => null,
            'acted_at' => null,
        ];
    }
}
