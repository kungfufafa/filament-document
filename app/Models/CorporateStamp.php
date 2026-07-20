<?php

namespace App\Models;

use Database\Factories\CorporateStampFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorporateStamp extends Model
{
    /** @use HasFactory<CorporateStampFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'website',
        'logo_path',
        'color',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::saving(function (CorporateStamp $stamp): void {
            if (! $stamp->is_default) {
                return;
            }

            static::query()
                ->where('user_id', $stamp->user_id)
                ->when($stamp->exists, fn ($query) => $query->whereKeyNot($stamp->getKey()))
                ->update(['is_default' => false]);
        });
    }
}
