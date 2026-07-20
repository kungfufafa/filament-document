<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('title'),
                            TextEntry::make('status')
                                ->badge(),
                            TextEntry::make('folder.name')
                                ->label('Folder')
                                ->placeholder('—'),
                            TextEntry::make('original_filename')
                                ->label('Original file')
                                ->icon(Heroicon::OutlinedDocument)
                                ->placeholder('—'),
                            TextEntry::make('sent_at')
                                ->dateTime()
                                ->placeholder('—'),
                            TextEntry::make('expires_at')
                                ->dateTime()
                                ->placeholder('—'),
                            TextEntry::make('sequential_signing')
                                ->label('Sequential signing')
                                ->formatStateUsing(fn (?bool $state): string => $state ? 'Yes' : 'No'),
                            TextEntry::make('email_subject')
                                ->placeholder('—')
                                ->columnSpanFull(),
                            TextEntry::make('message')
                                ->placeholder('—')
                                ->columnSpanFull(),
                        ]),
                    ]),
                Section::make('Recipients')
                    ->schema([
                        RepeatableEntry::make('recipients')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('email'),
                                TextEntry::make('role')
                                    ->badge(),
                                TextEntry::make('status')
                                    ->badge(),
                                TextEntry::make('signing_order')
                                    ->label('Order'),
                            ])
                            ->columns(5),
                    ]),
                Section::make('Activity timeline')
                    ->schema([
                        RepeatableEntry::make('activities')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('When')
                                    ->dateTime(),
                                TextEntry::make('type')
                                    ->badge(),
                                TextEntry::make('description')
                                    ->columnSpan(2),
                                TextEntry::make('actor_name')
                                    ->label('Actor')
                                    ->placeholder('—'),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }
}
