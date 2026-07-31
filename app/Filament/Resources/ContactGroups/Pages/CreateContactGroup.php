<?php

namespace App\Filament\Resources\ContactGroups\Pages;

use App\Filament\Resources\ContactGroups\ContactGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContactGroup extends CreateRecord
{
    protected static string $resource = ContactGroupResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
