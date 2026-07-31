<?php

namespace App\Filament\Resources\Templates\Schemas;

use App\Enums\TemplateStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Template details')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('file_path')
                            ->label('Document file')
                            ->disk(config('filesystems.default'))
                            ->directory('templates')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->downloadable()
                            ->openable(),
                        Toggle::make('sequential_signing')
                            ->label('Sequential signing')
                            ->default(false),
                        Select::make('status')
                            ->options(TemplateStatus::class)
                            ->default(TemplateStatus::Active->value)
                            ->required(),
                    ]),
            ]);
    }
}
