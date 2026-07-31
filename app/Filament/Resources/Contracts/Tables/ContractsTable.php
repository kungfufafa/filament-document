<?php

namespace App\Filament\Resources\Contracts\Tables;

use App\Enums\ContractStatus;
use App\Models\Contract;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContractsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('counterparty')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('contract_value')
                    ->money(fn (Contract $record): string => $record->currency ?? 'IDR')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('effective_date')
                    ->date()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('expiry_date')
                    ->date()
                    ->sortable()
                    ->color(fn (Contract $record): ?string => $record->isExpiringSoon() ? 'warning' : null)
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordClasses(fn (Contract $record): ?string => $record->isExpiringSoon()
                ? 'fi-ta-record-expiring'
                : null)
            ->filters([
                SelectFilter::make('status')
                    ->options(ContractStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
