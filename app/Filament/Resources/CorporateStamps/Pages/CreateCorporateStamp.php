<?php

namespace App\Filament\Resources\CorporateStamps\Pages;

use App\Filament\Resources\CorporateStamps\CorporateStampResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCorporateStamp extends CreateRecord
{
    protected static string $resource = CorporateStampResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
