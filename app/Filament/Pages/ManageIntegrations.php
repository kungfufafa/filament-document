<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroup;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageIntegrations extends Page
{
    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Settings;

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static ?string $navigationLabel = 'Integrations';

    protected static ?string $title = 'Manage integrations';

    protected string $view = 'filament.pages.manage-integrations';

    /**
     * @return list<array{name: string, description: string, connected: bool}>
     */
    public function getIntegrations(): array
    {
        return [
            [
                'name' => 'Google Drive',
                'description' => 'Import documents directly from Google Drive.',
                'connected' => false,
            ],
            [
                'name' => 'Dropbox',
                'description' => 'Sync templates and signed documents with Dropbox.',
                'connected' => false,
            ],
            [
                'name' => 'OneDrive',
                'description' => 'Connect your Microsoft OneDrive account.',
                'connected' => false,
            ],
        ];
    }
}
