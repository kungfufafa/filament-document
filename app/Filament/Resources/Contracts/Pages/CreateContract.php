<?php

namespace App\Filament\Resources\Contracts\Pages;

use App\Enums\ContractStatus;
use App\Filament\Resources\Contracts\ContractResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] ??= ContractStatus::Draft->value;

        return $data;
    }
}
