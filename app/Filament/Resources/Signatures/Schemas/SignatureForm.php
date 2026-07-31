<?php

namespace App\Filament\Resources\Signatures\Schemas;

use App\Enums\SignatureMethod;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class SignatureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Signature')
                    ->schema([
                        Select::make('method')
                            ->options(SignatureMethod::class)
                            ->default(SignatureMethod::Type->value)
                            ->required()
                            ->live(),
                        TextInput::make('fullname')
                            ->label('Full name')
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => static::methodIs($get, SignatureMethod::Type)),
                        TextInput::make('initials')
                            ->maxLength(10)
                            ->visible(fn (Get $get): bool => static::methodIs($get, SignatureMethod::Type)),
                        Textarea::make('draw_data')
                            ->label('Draw data')
                            ->rows(4)
                            ->visible(fn (Get $get): bool => static::methodIs($get, SignatureMethod::Draw)),
                        FileUpload::make('image_path')
                            ->label('Signature image')
                            ->disk('local')
                            ->directory('signatures')
                            ->image()
                            ->visible(fn (Get $get): bool => static::methodIs($get, SignatureMethod::Upload)),
                        Toggle::make('is_default')
                            ->label('Default signature')
                            ->default(false),
                    ]),
            ]);
    }

    protected static function methodIs(Get $get, SignatureMethod $method): bool
    {
        $state = $get('method');

        if ($state instanceof SignatureMethod) {
            return $state === $method;
        }

        return SignatureMethod::tryFrom((string) ($state ?? '')) === $method;
    }
}
