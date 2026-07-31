<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Enums\DocumentStatus;
use App\Models\Folder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Document')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('status')
                            ->options(DocumentStatus::class)
                            ->required(),
                        Select::make('folder_id')
                            ->label('Folder')
                            ->options(fn (): array => Folder::query()
                                ->where('user_id', Auth::id())
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable(),
                        TextInput::make('document_type')
                            ->maxLength(255),
                        Toggle::make('sequential_signing')
                            ->label('Sign in order'),
                        TextInput::make('email_subject')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('message')
                            ->rows(4)
                            ->columnSpanFull(),
                        DateTimePicker::make('expires_at'),
                    ])
                    ->columns(2),
            ]);
    }
}
