<?php

namespace App\Filament\Resources\EmeteraiUsages;

use App\Enums\NavigationGroup;
use App\Filament\Resources\EmeteraiUsages\Pages\ListEmeteraiUsages;
use App\Filament\Resources\EmeteraiUsages\Schemas\EmeteraiUsageForm;
use App\Filament\Resources\EmeteraiUsages\Tables\EmeteraiUsagesTable;
use App\Models\EmeteraiUsage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmeteraiUsageResource extends Resource
{
    protected static ?string $model = EmeteraiUsage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Emeterai;

    protected static ?string $navigationLabel = 'Usage history';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'eMeterai usage';

    protected static ?string $pluralModelLabel = 'eMeterai usage';

    public static function form(Schema $schema): Schema
    {
        return EmeteraiUsageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmeteraiUsagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmeteraiUsages::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}
