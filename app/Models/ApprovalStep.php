<?php

namespace App\Models;

use App\Enums\ApprovalStepStatus;
use Database\Factories\ApprovalStepFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalStep extends Model
{
    /** @use HasFactory<ApprovalStepFactory> */
    use HasFactory;

    protected $fillable = [
        'approval_id',
        'approver_name',
        'approver_email',
        'step_order',
        'status',
        'notes',
        'acted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApprovalStepStatus::class,
            'step_order' => 'integer',
            'acted_at' => 'datetime',
        ];
    }

    public function approval(): BelongsTo
    {
        return $this->belongsTo(Approval::class);
    }
}
