<?php

namespace App\Filament\Resources\Signatures;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Signatures\Pages\CreateSignature;
use App\Filament\Resources\Signatures\Pages\EditSignature;
use App\Filament\Resources\Signatures\Pages\ListSignatures;
use App\Filament\Resources\Signatures\Schemas\SignatureForm;
use App\Filament\Resources\Signatures\Tables\SignaturesTable;
use App\Models\Signature;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SignatureResource extends Resource
{
    protected static ?string $model = Signature::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Settings;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Signatures';

    protected static ?string $modelLabel = 'signature';

    public static function form(Schema $schema): Schema
    {
        return SignatureForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SignaturesTable::configure($table);
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
            'index' => ListSignatures::route('/'),
            'create' => CreateSignature::route('/create'),
            'edit' => EditSignature::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}
