<?php

namespace App\Filament\Resources\Templates\Tables;

use App\Actions\Templates\CreateDocumentFromTemplate;
use App\Enums\TemplateStatus;
use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('original_filename')
                    ->label('File')
                    ->toggleable(),
                IconColumn::make('sequential_signing')
                    ->label('Sequential')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('page_count')
                    ->label('Pages')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Last updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options(TemplateStatus::class),
            ])
            ->recordActions([
                Action::make('useTemplate')
                    ->label('Use template')
                    ->icon(Heroicon::OutlinedDocumentPlus)
                    ->visible(fn ($record): bool => $record->deleted_at === null)
                    ->action(function ($record, CreateDocumentFromTemplate $createDocumentFromTemplate) {
                        $document = $createDocumentFromTemplate->handle($record);

                        Notification::make()
                            ->title('Document draft created')
                            ->success()
                            ->send();

                        return redirect()->to(DocumentResource::getUrl('edit', ['record' => $document]));
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
