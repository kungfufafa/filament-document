<?php

namespace App\Models;

use Database\Factories\EmeteraiBalanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmeteraiBalance extends Model
{
    /** @use HasFactory<EmeteraiBalanceFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'available',
        'used',
    ];

    protected function casts(): array
    {
        return [
            'available' => 'integer',
            'used' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function forUser(User $user): self
    {
        return static::firstOrCreate(
            ['user_id' => $user->id],
            ['available' => 0, 'used' => 0],
        );
    }
}
