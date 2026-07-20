<?php

namespace App\Filament\Resources\Approvals\Pages;

use App\Enums\ApprovalStatus;
use App\Filament\Resources\Approvals\ApprovalResource;
use App\Models\Approval;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditApproval extends EditRecord
{
    protected static string $resource = ApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('submit')
                ->label('Submit')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->visible(fn (): bool => $this->record->status === ApprovalStatus::Draft)
                ->requiresConfirmation()
                ->action(function (): void {
                    /** @var Approval $record */
                    $record = $this->record;

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

                    $this->refreshFormData(['status', 'submitted_at']);
                }),
            Action::make('approve')
                ->label('Approve step')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => $this->record->status === ApprovalStatus::Pending)
                ->requiresConfirmation()
                ->action(function (): void {
                    /** @var Approval $record */
                    $record = $this->record;

                    $record->approveCurrentStep();

                    Notification::make()
                        ->title('Step approved')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'completed_at']);
                }),
            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => $this->record->status === ApprovalStatus::Pending)
                ->requiresConfirmation()
                ->action(function (): void {
                    /** @var Approval $record */
                    $record = $this->record;

                    $record->rejectCurrentStep();

                    Notification::make()
                        ->title('Approval rejected')
                        ->warning()
                        ->send();

                    $this->refreshFormData(['status', 'completed_at']);
                }),
            DeleteAction::make(),
        ];
    }
}
