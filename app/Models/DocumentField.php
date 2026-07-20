<?php

namespace App\Models;

use App\Enums\FieldType;
use Database\Factories\DocumentFieldFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentField extends Model
{
    /** @use HasFactory<DocumentFieldFactory> */
    use HasFactory;

    protected $fillable = [
        'document_id',
        'document_recipient_id',
        'type',
        'page',
        'x',
        'y',
        'width',
        'height',
        'required',
        'value',
        'filled_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => FieldType::class,
            'page' => 'integer',
            'x' => 'float',
            'y' => 'float',
            'width' => 'float',
            'height' => 'float',
            'required' => 'boolean',
            'filled_at' => 'datetime',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(DocumentRecipient::class, 'document_recipient_id');
    }
}
