<?php

namespace App\Models;

use App\Enums\DocumentActivityType;
use Database\Factories\DocumentActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentActivity extends Model
{
    /** @use HasFactory<DocumentActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'document_recipient_id',
        'type',
        'actor_name',
        'actor_email',
        'description',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'type' => DocumentActivityType::class,
            'meta' => 'array',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(DocumentRecipient::class, 'document_recipient_id');
    }
}
