<?php

namespace App\Filament\Resources\Approvals\Tables;

use App\Enums\ApprovalStatus;
use App\Models\Approval;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApprovalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->recordActions([
                Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->visible(fn (Approval $record): bool => $record->status === ApprovalStatus::Draft)
                    ->requiresConfirmation()
                    ->action(function (Approval $record): void {
                        if ($record->steps()->count() === 0) {
                            Notification::make()
                                ->title('Add at least one approval step before submitting.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->submit();

                        Notification::make()
                            ->title('Approval submitted')
                            ->success()
                            ->send();
                    }),
                Action::make('approve')
                    ->label('Approve step')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Approval $record): bool => $record->status === ApprovalStatus::Pending)
                    ->requiresConfirmation()
                    ->action(function (Approval $record): void {
                        $record->approveCurrentStep();

                        Notification::make()
                            ->title('Step approved')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Approval $record): bool => $record->status === ApprovalStatus::Pending)
                    ->requiresConfirmation()
                    ->action(function (Approval $record): void {
                        $record->rejectCurrentStep();

                        Notification::make()
                            ->title('Approval rejected')
                            ->warning()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
