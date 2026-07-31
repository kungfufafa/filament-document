<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Actions\Documents\RecordDocumentActivity;
use App\Actions\Documents\SendDocument;
use App\Enums\DocumentActivityType;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\Folder;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Last modified')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('folder.name')
                    ->label('Folder')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('resend')
                        ->label('Resend')
                        ->icon(Heroicon::OutlinedPaperAirplane)
                        ->visible(fn (Document $record): bool => in_array($record->status, [
                            DocumentStatus::Pending,
                            DocumentStatus::NeedsToSign,
                        ], true))
                        ->requiresConfirmation()
                        ->action(function (Document $record, SendDocument $sendDocument, RecordDocumentActivity $recordActivity): void {
                            $sendDocument->handle($record);

                            $recordActivity->handle(
                                $record->fresh(),
                                DocumentActivityType::Resent,
                                'Document resent to recipients.',
                                auth()->user(),
                            );

                            Notification::make()
                                ->title('Document resent')
                                ->success()
                                ->send();
                        }),
                    Action::make('download')
                        ->label('Download')
                        ->icon(Heroicon::OutlinedArrowDownTray)
                        ->action(function (Document $record, RecordDocumentActivity $recordActivity) {
                            $recordActivity->handle(
                                $record,
                                DocumentActivityType::Downloaded,
                                'Document downloaded.',
                                auth()->user(),
                            );

                            if (! Storage::disk(config('filesystems.default'))->exists($record->file_path)) {
                                Notification::make()
                                    ->title('File not found')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            return Storage::disk(config('filesystems.default'))->download(
                                $record->file_path,
                                $record->original_filename ?: basename($record->file_path),
                            );
                        }),
                    Action::make('moveToFolder')
                        ->label('Move to folder')
                        ->icon(Heroicon::OutlinedFolderArrowDown)
                        ->schema([
                            Select::make('folder_id')
                                ->label('Folder')
                                ->options(fn (): array => Folder::query()
                                    ->where('user_id', auth()->id())
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all())
                                ->nullable()
                                ->searchable(),
                        ])
                        ->action(function (Document $record, array $data, RecordDocumentActivity $recordActivity): void {
                            $record->update(['folder_id' => $data['folder_id'] ?? null]);

                            $folderName = $data['folder_id']
                                ? Folder::query()->find($data['folder_id'])?->name
                                : null;

                            $recordActivity->handle(
                                $record->fresh(),
                                DocumentActivityType::Moved,
                                $folderName
                                    ? "Moved to folder \"{$folderName}\"."
                                    : 'Removed from folder.',
                                auth()->user(),
                            );

                            Notification::make()
                                ->title('Document moved')
                                ->success()
                                ->send();
                        }),
                    Action::make('void')
                        ->label('Void')
                        ->icon(Heroicon::OutlinedNoSymbol)
                        ->color('danger')
                        ->visible(fn (Document $record): bool => ! in_array($record->status, [
                            DocumentStatus::Voided,
                            DocumentStatus::Completed,
                            DocumentStatus::Trash,
                        ], true))
                        ->requiresConfirmation()
                        ->action(function (Document $record, RecordDocumentActivity $recordActivity): void {
                            $record->update(['status' => DocumentStatus::Voided]);

                            $recordActivity->handle(
                                $record->fresh(),
                                DocumentActivityType::Voided,
                                'Document voided.',
                                auth()->user(),
                            );

                            Notification::make()
                                ->title('Document voided')
                                ->success()
                                ->send();
                        }),
                    Action::make('softDelete')
                        ->label('Move to trash')
                        ->icon(Heroicon::OutlinedTrash)
                        ->color('danger')
                        ->visible(fn (Document $record): bool => $record->status !== DocumentStatus::Trash)
                        ->requiresConfirmation()
                        ->action(function (Document $record): void {
                            $record->update(['status' => DocumentStatus::Trash]);
                            $record->delete();

                            Notification::make()
                                ->title('Document moved to trash')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Move to trash')
                        ->action(function ($records): void {
                            $records->each(function (Document $record): void {
                                $record->update(['status' => DocumentStatus::Trash]);
                                $record->delete();
                            });
                        }),
                ]),
            ]);
    }
}
