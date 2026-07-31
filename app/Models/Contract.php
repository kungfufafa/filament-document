<?php

namespace App\Models;

use App\Enums\ContractStatus;
use Database\Factories\ContractFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    /** @use HasFactory<ContractFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'document_id',
        'title',
        'counterparty',
        'contract_value',
        'currency',
        'effective_date',
        'expiry_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContractStatus::class,
            'contract_value' => 'decimal:2',
            'effective_date' => 'date',
            'expiry_date' => 'date',
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

    public function isExpiringSoon(): bool
    {
        if ($this->expiry_date === null) {
            return false;
        }

        return $this->expiry_date->isFuture()
            && $this->expiry_date->lte(now()->addDays(30));
    }
}
