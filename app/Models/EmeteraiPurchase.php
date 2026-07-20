<?php

namespace App\Models;

use App\Enums\EmeteraiPurchaseStatus;
use Database\Factories\EmeteraiPurchaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EmeteraiPurchase extends Model
{
    /** @use HasFactory<EmeteraiPurchaseFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quantity',
        'unit_price',
        'total_amount',
        'status',
        'transaction_number',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'status' => EmeteraiPurchaseStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateTransactionNumber(): string
    {
        return 'TRX-'.now()->format('Ymd').'-'.strtoupper(Str::random(8));
    }
}
