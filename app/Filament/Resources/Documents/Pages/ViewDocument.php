<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General information')
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('original_filename')->label('File'),
                        TextEntry::make('page_count'),
                        TextEntry::make('folder.name')->label('Folder')->placeholder('—'),
                        TextEntry::make('sent_at')->dateTime()->placeholder('—'),
                        TextEntry::make('completed_at')->dateTime()->placeholder('—'),
                        TextEntry::make('expires_at')->dateTime()->placeholder('—'),
                        TextEntry::make('id')->label('Reference ID')->copyable(),
                    ])
                    ->columns(2),
                Section::make('Recipients')
                    ->schema([
                        RepeatableEntry::make('recipients')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('email'),
                                TextEntry::make('role')->badge(),
                                TextEntry::make('status')->badge(),
                                TextEntry::make('signing_order')->label('Order'),
                                TextEntry::make('signed_at')->dateTime()->placeholder('—'),
                                TextEntry::make('access_token')
                                    ->label('Signing link')
                                    ->formatStateUsing(fn (?string $state): string => $state ? route('signing.show', $state) : '—')
                                    ->copyable(),
                            ])
                            ->columns(3),
                    ]),
                Section::make('Activities')
                    ->schema([
                        RepeatableEntry::make('activities')
                            ->schema([
                                TextEntry::make('created_at')->dateTime()->label('When'),
                                TextEntry::make('type')->badge(),
                                TextEntry::make('description')->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }
}
