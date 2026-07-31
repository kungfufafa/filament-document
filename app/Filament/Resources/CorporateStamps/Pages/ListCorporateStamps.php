<?php

namespace App\Filament\Resources\CorporateStamps\Pages;

use App\Filament\Resources\CorporateStamps\CorporateStampResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCorporateStamps extends ListRecords
{
    protected static string $resource = CorporateStampResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
