<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use App\Enums\ApprovalStepStatus;
use App\Enums\ApprovalType;
use Database\Factories\ApprovalFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Approval extends Model
{
    /** @use HasFactory<ApprovalFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'document_id',
        'title',
        'message',
        'type',
        'status',
        'submitted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => ApprovalType::class,
            'status' => ApprovalStatus::class,
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalStep::class)->orderBy('step_order');
    }

    public function currentStep(): ?ApprovalStep
    {
        return $this->steps()
            ->where('status', ApprovalStepStatus::Pending)
            ->orderBy('step_order')
            ->first();
    }

    public function submit(): void
    {
        $this->update([
            'status' => ApprovalStatus::Pending,
            'submitted_at' => now(),
        ]);

        $this->steps()->update([
            'status' => ApprovalStepStatus::Pending,
            'acted_at' => null,
        ]);
    }

    public function approveCurrentStep(): void
    {
        $step = $this->currentStep();

        if ($step === null) {
            return;
        }

        $step->update([
            'status' => ApprovalStepStatus::Approved,
            'acted_at' => now(),
        ]);

        $hasPendingSteps = $this->steps()
            ->where('status', ApprovalStepStatus::Pending)
            ->exists();

        if (! $hasPendingSteps) {
            $this->update([
                'status' => ApprovalStatus::Approved,
                'completed_at' => now(),
            ]);
        }
    }

    public function rejectCurrentStep(?string $notes = null): void
    {
        $step = $this->currentStep();

        if ($step === null) {
            return;
        }

        $step->update([
            'status' => ApprovalStepStatus::Rejected,
            'notes' => $notes,
            'acted_at' => now(),
        ]);

        $this->update([
            'status' => ApprovalStatus::Rejected,
            'completed_at' => now(),
        ]);
    }
}
