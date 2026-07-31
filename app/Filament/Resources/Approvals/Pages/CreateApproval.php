<?php

namespace App\Filament\Resources\Approvals\Pages;

use App\Enums\ApprovalStatus;
use App\Filament\Resources\Approvals\ApprovalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApproval extends CreateRecord
{
    protected static string $resource = ApprovalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = ApprovalStatus::Draft->value;

        return $data;
    }
}
