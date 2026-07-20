<?php

namespace App\Filament\Pages;

use App\Enums\EmeteraiPurchaseStatus;
use App\Enums\NavigationGroup;
use App\Filament\Widgets\EmeteraiBalanceOverview;
use App\Models\Document;
use App\Models\EmeteraiBalance;
use App\Models\EmeteraiPurchase;
use App\Models\EmeteraiUsage;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class EmeteraiDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Emeterai;

    protected static ?string $navigationLabel = 'eMeterai';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'eMeterai';

    protected string $view = 'filament.pages.emeterai-dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            EmeteraiBalanceOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('buyEmeterai')
                ->label('Buy eMeterai')
                ->icon('heroicon-o-shopping-cart')
                ->color('primary')
                ->schema([
                    TextInput::make('quantity')
                        ->label('Quantity')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->default(1),
                ])
                ->action(function (array $data): void {
                    $quantity = (int) $data['quantity'];
                    $unitPrice = 10000;

                    EmeteraiPurchase::create([
                        'user_id' => auth()->id(),
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_amount' => $quantity * $unitPrice,
                        'status' => EmeteraiPurchaseStatus::Completed,
                        'transaction_number' => EmeteraiPurchase::generateTransactionNumber(),
                    ]);

                    $user = auth()->user();
                    abort_unless($user instanceof User, 403);

                    $balance = EmeteraiBalance::forUser($user);
                    $balance->increment('available', $quantity);

                    Notification::make()
                        ->title("Purchased {$quantity} eMeterai stamp(s)")
                        ->success()
                        ->send();

                    $this->redirect(static::getUrl());
                }),
            Action::make('useEmeterai')
                ->label('Use eMeterai')
                ->icon('heroicon-o-qr-code')
                ->color('success')
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

                    $this->redirect(static::getUrl());
                }),
        ];
    }
}
