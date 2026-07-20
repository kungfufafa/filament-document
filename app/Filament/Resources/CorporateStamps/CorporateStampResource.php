<?php

namespace App\Filament\Resources\CorporateStamps;

use App\Enums\NavigationGroup;
use App\Filament\Resources\CorporateStamps\Pages\CreateCorporateStamp;
use App\Filament\Resources\CorporateStamps\Pages\EditCorporateStamp;
use App\Filament\Resources\CorporateStamps\Pages\ListCorporateStamps;
use App\Filament\Resources\CorporateStamps\Schemas\CorporateStampForm;
use App\Filament\Resources\CorporateStamps\Tables\CorporateStampsTable;
use App\Models\CorporateStamp;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CorporateStampResource extends Resource
{
    protected static ?string $model = CorporateStamp::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Settings;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Corporate stamps';

    protected static ?string $modelLabel = 'corporate stamp';

    protected static ?string $recordTitleAttribute = 'company_name';

    public static function form(Schema $schema): Schema
    {
        return CorporateStampForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CorporateStampsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCorporateStamps::route('/'),
            'create' => CreateCorporateStamp::route('/create'),
            'edit' => EditCorporateStamp::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}
