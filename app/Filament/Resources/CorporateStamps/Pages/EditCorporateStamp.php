<?php

namespace App\Filament\Resources\CorporateStamps\Pages;

use App\Filament\Resources\CorporateStamps\CorporateStampResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCorporateStamp extends EditRecord
{
    protected static string $resource = CorporateStampResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
