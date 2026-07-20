<?php

namespace App\Filament\Widgets;

use App\Enums\DocumentStatus;
use App\Models\Document;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DocumentOverviewWidget extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Document overview';

    protected function getStats(): array
    {
        $userId = auth()->id();

        $baseQuery = Document::query()->where('user_id', $userId);

        return [
            Stat::make('Needs to sign', (clone $baseQuery)
                ->where('status', DocumentStatus::NeedsToSign)
                ->whereNull('deleted_at')
                ->count())
                ->description('Awaiting signature')
                ->descriptionIcon(Heroicon::OutlinedPencilSquare)
                ->color('warning'),
            Stat::make('Pending', (clone $baseQuery)
                ->where('status', DocumentStatus::Pending)
                ->whereNull('deleted_at')
                ->count())
                ->description('Out for signature')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color('info'),
            Stat::make('Expiring soon', (clone $baseQuery)
                ->whereNotNull('expires_at')
                ->whereBetween('expires_at', [now(), now()->addDays(7)])
                ->whereNotIn('status', [
                    DocumentStatus::Completed,
                    DocumentStatus::Voided,
                    DocumentStatus::Trash,
                    DocumentStatus::Declined,
                ])
                ->whereNull('deleted_at')
                ->count())
                ->description('Within 7 days')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->color('danger'),
            Stat::make('Completed', (clone $baseQuery)
                ->where('status', DocumentStatus::Completed)
                ->whereNull('deleted_at')
                ->count())
                ->description('Fully signed')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
        ];
    }
}
