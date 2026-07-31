<?php

namespace App\Filament\Resources\Contacts\Schemas;

use App\Enums\ContactVisibility;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('job_title')
                                ->label('Job title')
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('company')
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('phone')
                                ->tel()
                                ->maxLength(255)
                                ->columnSpan(1),
                            Select::make('visibility')
                                ->options(ContactVisibility::class)
                                ->default(ContactVisibility::Personal->value)
                                ->required()
                                ->columnSpan(1),
                            Textarea::make('address')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    ]),
                Section::make('Groups')
                    ->schema([
                        Select::make('groups')
                            ->relationship(
                                name: 'groups',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query->where('user_id', auth()->id()),
                            )
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }
}
