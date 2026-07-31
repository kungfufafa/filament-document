<?php

namespace App\Models;

use App\Enums\SignatureMethod;
use Database\Factories\SignatureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Signature extends Model
{
    /** @use HasFactory<SignatureFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'method',
        'fullname',
        'initials',
        'image_path',
        'draw_data',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'method' => SignatureMethod::class,
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::saving(function (Signature $signature): void {
            if (! $signature->is_default) {
                return;
            }

            static::query()
                ->where('user_id', $signature->user_id)
                ->when($signature->exists, fn ($query) => $query->whereKeyNot($signature->getKey()))
                ->update(['is_default' => false]);
        });
    }
}
