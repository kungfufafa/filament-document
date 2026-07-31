<?php

namespace App\Filament\Resources\ContactGroups;

use App\Enums\NavigationGroup;
use App\Filament\Resources\ContactGroups\Pages\CreateContactGroup;
use App\Filament\Resources\ContactGroups\Pages\EditContactGroup;
use App\Filament\Resources\ContactGroups\Pages\ListContactGroups;
use App\Filament\Resources\ContactGroups\RelationManagers\ContactsRelationManager;
use App\Filament\Resources\ContactGroups\Schemas\ContactGroupForm;
use App\Filament\Resources\ContactGroups\Tables\ContactGroupsTable;
use App\Models\ContactGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ContactGroupResource extends Resource
{
    protected static ?string $model = ContactGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Contacts;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Contact groups';

    protected static ?string $modelLabel = 'contact group';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ContactGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ContactsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactGroups::route('/'),
            'create' => CreateContactGroup::route('/create'),
            'edit' => EditContactGroup::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}
