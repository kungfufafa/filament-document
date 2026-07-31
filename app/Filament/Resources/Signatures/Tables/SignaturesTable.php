<?php

namespace App\Filament\Resources\Signatures\Tables;

use App\Enums\SignatureMethod;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SignaturesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('method')
                    ->badge()
                    ->sortable(),
                TextColumn::make('fullname')
                    ->label('Full name')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('initials')
                    ->placeholder('—')
                    ->toggleable(),
                ImageColumn::make('image_path')
                    ->label('Image')
                    ->disk(config('filesystems.default'))
                    ->toggleable(),
                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Last updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('method')
                    ->options(SignatureMethod::class),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('is_default', 'desc');
    }
}
