<?php

namespace App\Models;

use Database\Factories\EmeteraiUsageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EmeteraiUsage extends Model
{
    /** @use HasFactory<EmeteraiUsageFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_id',
        'emeterai_id',
        'document_name',
        'stamped_at',
        'placed_at',
    ];

    protected function casts(): array
    {
        return [
            'stamped_at' => 'datetime',
            'placed_at' => 'datetime',
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

    public static function generateEmeteraiId(): string
    {
        return 'EM-'.strtoupper(Str::random(12));
    }
}
