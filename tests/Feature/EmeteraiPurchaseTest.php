<?php

namespace Tests\Feature;

use App\Enums\EmeteraiPurchaseStatus;
use App\Models\EmeteraiBalance;
use App\Models\EmeteraiPurchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmeteraiPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_buying_emeterai_increments_available_balance(): void
    {
        $user = User::factory()->create();
        $balance = EmeteraiBalance::forUser($user);

        $this->assertSame(0, $balance->available);

        EmeteraiPurchase::create([
            'user_id' => $user->id,
            'quantity' => 5,
            'unit_price' => 10000,
            'total_amount' => 50000,
            'status' => EmeteraiPurchaseStatus::Completed,
            'transaction_number' => EmeteraiPurchase::generateTransactionNumber(),
        ]);

        $balance->increment('available', 5);

        $this->assertSame(5, $balance->fresh()->available);
        $this->assertDatabaseCount('emeterai_purchases', 1);
    }
}
