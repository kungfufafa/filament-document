<?php

namespace App\Filament\Resources\Approvals\Schemas;

use App\Enums\ApprovalStatus;
use App\Enums\ApprovalType;
use App\Models\Approval;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ApprovalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Approval details')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('type')
                            ->options(ApprovalType::class)
                            ->required()
                            ->live()
                            ->default(ApprovalType::WithDocument->value),
                        Select::make('document_id')
                            ->label('Document')
                            ->relationship(
                                'document',
                                'title',
                                fn (Builder $query): Builder => $query->where('user_id', auth()->id()),
                            )
                            ->searchable()
                            ->preload()
                            ->visible(function (Get $get): bool {
                                $type = $get('type');

                                if ($type instanceof ApprovalType) {
                                    return $type === ApprovalType::WithDocument;
                                }

                                return $type === ApprovalType::WithDocument->value;
                            }),
                        Textarea::make('message')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Approval steps')
                    ->schema([
                        Repeater::make('steps')
                            ->relationship()
                            ->schema([
                                TextInput::make('approver_name')
                                    ->label('Approver name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('approver_email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('step_order')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1),
                            ])
                            ->orderColumn('step_order')
                            ->columns(3)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->addActionLabel('Add step')
                            ->columnSpanFull()
                            ->disabled(fn (?Approval $record): bool => $record !== null && $record->status !== ApprovalStatus::Draft),
                    ]),
            ]);
    }
}
