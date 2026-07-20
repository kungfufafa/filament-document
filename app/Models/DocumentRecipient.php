<?php

namespace App\Models;

use App\Enums\RecipientRole;
use App\Enums\RecipientStatus;
use Database\Factories\DocumentRecipientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DocumentRecipient extends Model
{
    /** @use HasFactory<DocumentRecipientFactory> */
    use HasFactory;

    protected $fillable = [
        'document_id',
        'name',
        'email',
        'role',
        'status',
        'signing_order',
        'access_token',
        'viewed_at',
        'signed_at',
        'declined_at',
        'decline_reason',
    ];

    protected function casts(): array
    {
        return [
            'role' => RecipientRole::class,
            'status' => RecipientStatus::class,
            'signing_order' => 'integer',
            'viewed_at' => 'datetime',
            'signed_at' => 'datetime',
            'declined_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (DocumentRecipient $recipient): void {
            if (blank($recipient->access_token)) {
                $recipient->access_token = (string) Str::uuid();
            }
        });
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(DocumentField::class);
    }

    public function getRouteKeyName(): string
    {
        return 'access_token';
    }
}
