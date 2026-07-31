<?php

namespace App\Filament\Resources\EmeteraiUsages\Pages;

use App\Filament\Resources\EmeteraiUsages\EmeteraiUsageResource;
use App\Models\Document;
use App\Models\EmeteraiBalance;
use App\Models\EmeteraiUsage;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListEmeteraiUsages extends ListRecords
{
    protected static string $resource = EmeteraiUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('useEmeterai')
                ->label('Use eMeterai')
                ->icon('heroicon-o-qr-code')
                ->color('primary')
                ->schema([
                    Select::make('document_id')
                        ->label('Document')
                        ->options(fn (): array => Document::query()
                            ->where('user_id', auth()->id())
                            ->orderBy('title')
                            ->pluck('title', 'id')
                            ->all())
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(function (?string $state, callable $set): void {
                            if ($state === null) {
                                return;
                            }

                            $document = Document::query()->find($state);
                            $set('document_name', $document?->title);
                        }),
                    TextInput::make('document_name')
                        ->label('Document name')
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $user = auth()->user();
                    abort_unless($user instanceof User, 403);

                    $balance = EmeteraiBalance::forUser($user);

                    if ($balance->available < 1) {
                        Notification::make()
                            ->title('Insufficient eMeterai balance')
                            ->body('Buy more eMeterai stamps before using.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $document = isset($data['document_id'])
                        ? Document::query()->find($data['document_id'])
                        : null;

                    EmeteraiUsage::create([
                        'user_id' => auth()->id(),
                        'document_id' => $document?->id,
                        'emeterai_id' => EmeteraiUsage::generateEmeteraiId(),
                        'document_name' => $data['document_name'] ?? $document?->title,
                        'stamped_at' => now(),
                        'placed_at' => now(),
                    ]);

                    $balance->decrement('available');
                    $balance->increment('used');

                    Notification::make()
                        ->title('eMeterai stamp applied')
                        ->success()
                        ->send();
                }),
        ];
    }
}
