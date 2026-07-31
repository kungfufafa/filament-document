<?php

namespace App\Filament\Resources\CorporateStamps\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CorporateStampForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Corporate stamp')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Company name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->disk(config('filesystems.default'))
                            ->directory('corporate-stamps')
                            ->image(),
                        ColorPicker::make('color')
                            ->default('#22c55e'),
                        Toggle::make('is_default')
                            ->label('Default stamp')
                            ->default(false),
                    ]),
            ]);
    }
}
