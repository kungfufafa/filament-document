<?php

namespace App\Filament\Widgets;

use App\Models\EmeteraiBalance;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmeteraiBalanceOverview extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $balance = EmeteraiBalance::forUser($user);

        return [
            Stat::make('Available', number_format($balance->available))
                ->description('Ready to use')
                ->color('success'),
            Stat::make('Used', number_format($balance->used))
                ->description('Total stamped')
                ->color('gray'),
        ];
    }
}
