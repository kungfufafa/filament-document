<?php

namespace App\Filament\Resources\EmeteraiUsages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmeteraiUsageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Usage details')
                    ->schema([
                        TextInput::make('emeterai_id')
                            ->label('eMeterai ID')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('document_name')
                            ->label('Document name')
                            ->maxLength(255),
                        DateTimePicker::make('stamped_at')
                            ->label('Stamped at'),
                        DateTimePicker::make('placed_at')
                            ->label('Placed at'),
                    ])
                    ->columns(2),
            ]);
    }
}
