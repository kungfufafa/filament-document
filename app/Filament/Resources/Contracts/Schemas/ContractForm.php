<?php

namespace App\Filament\Resources\Contracts\Schemas;

use App\Enums\ContractStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contract details')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('counterparty')
                            ->maxLength(255),
                        TextInput::make('contract_value')
                            ->numeric()
                            ->prefix('Rp')
                            ->step(0.01),
                        Select::make('currency')
                            ->options([
                                'IDR' => 'IDR',
                                'USD' => 'USD',
                                'EUR' => 'EUR',
                                'SGD' => 'SGD',
                            ])
                            ->default('IDR')
                            ->required(),
                        DatePicker::make('effective_date'),
                        DatePicker::make('expiry_date')
                            ->afterOrEqual('effective_date'),
                        Select::make('status')
                            ->options(ContractStatus::class)
                            ->required()
                            ->default(ContractStatus::Draft->value),
                        Select::make('document_id')
                            ->label('Document')
                            ->relationship(
                                'document',
                                'title',
                                fn (Builder $query): Builder => $query->where('user_id', auth()->id()),
                            )
                            ->searchable()
                            ->preload(),
                        Textarea::make('notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
