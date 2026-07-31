<?php

namespace App\Filament\Widgets;

use App\Enums\ContractStatus;
use App\Models\Contract;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContractStatsOverview extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        $userId = auth()->id();

        $activeCount = Contract::query()
            ->where('user_id', $userId)
            ->where('status', ContractStatus::Active)
            ->count();

        $expiringCount = Contract::query()
            ->where('user_id', $userId)
            ->where('status', ContractStatus::Active)
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->count();

        $expiredCount = Contract::query()
            ->where('user_id', $userId)
            ->where(function ($query): void {
                $query
                    ->where('status', ContractStatus::Expired)
                    ->orWhere(function ($query): void {
                        $query
                            ->whereNotNull('expiry_date')
                            ->whereDate('expiry_date', '<', now());
                    });
            })
            ->count();

        return [
            Stat::make('Active contracts', $activeCount)
                ->description('Currently active')
                ->color('success'),
            Stat::make('Expiring soon', $expiringCount)
                ->description('Within 30 days')
                ->color('warning'),
            Stat::make('Expired', $expiredCount)
                ->description('Past expiry date')
                ->color('danger'),
        ];
    }
}
