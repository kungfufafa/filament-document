<?php

namespace App\Filament\Resources\EmeteraiUsages\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmeteraiUsagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('emeterai_id')
                    ->label('eMeterai ID')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('document_name')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('stamped_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('placed_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
