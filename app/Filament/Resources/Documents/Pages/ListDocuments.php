<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Enums\DocumentStatus;
use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Widgets\DocumentOverviewWidget;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Upload document')
                ->icon(Heroicon::OutlinedArrowUpTray),
            Action::make('export')
                ->label('Export')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->action(function (): void {
                    Notification::make()
                        ->title('Export coming soon')
                        ->info()
                        ->send();
                }),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            DocumentOverviewWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('status', '!=', DocumentStatus::Trash)
                    ->whereNull('deleted_at')),
            'needs_to_sign' => Tab::make('Needs to sign')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('status', DocumentStatus::NeedsToSign)
                    ->whereNull('deleted_at')),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('status', DocumentStatus::Pending)
                    ->whereNull('deleted_at')),
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('status', DocumentStatus::Completed)
                    ->whereNull('deleted_at')),
            'voided' => Tab::make('Voided')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('status', DocumentStatus::Voided)
                    ->whereNull('deleted_at')),
            'declined' => Tab::make('Declined')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('status', DocumentStatus::Declined)
                    ->whereNull('deleted_at')),
            'drafts' => Tab::make('Drafts')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('status', DocumentStatus::Draft)
                    ->whereNull('deleted_at')),
            'trash' => Tab::make('Trash')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where(function (Builder $query): void {
                        $query->where('status', DocumentStatus::Trash)
                            ->orWhereNotNull('deleted_at');
                    })
                    ->withTrashed()),
        ];
    }
}
