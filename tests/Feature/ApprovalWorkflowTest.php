<?php

namespace Tests\Feature;

use App\Enums\ApprovalStatus;
use App\Enums\ApprovalStepStatus;
use App\Enums\ApprovalType;
use App\Models\Approval;
use App\Models\ApprovalStep;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_approval_can_be_submitted_and_fully_approved(): void
    {
        $user = User::factory()->create();
        $approval = Approval::factory()->for($user)->create([
            'status' => ApprovalStatus::Draft,
            'type' => ApprovalType::WithoutDocument,
        ]);

        ApprovalStep::factory()->for($approval)->create([
            'step_order' => 1,
            'status' => ApprovalStepStatus::Pending,
        ]);

        $approval->submit();
        $this->assertSame(ApprovalStatus::Pending, $approval->fresh()->status);

        $approval->approveCurrentStep();
        $this->assertSame(ApprovalStatus::Approved, $approval->fresh()->status);
    }
}
