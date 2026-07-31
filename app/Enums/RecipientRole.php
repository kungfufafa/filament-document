<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RecipientRole: string implements HasLabel
{
    case Signer = 'signer';
    case ReceivesCopy = 'receives_copy';
    case Approver = 'approver';

    public function getLabel(): string
    {
        return match ($this) {
            self::Signer => 'Needs to sign',
            self::ReceivesCopy => 'Receives a copy',
            self::Approver => 'Approver',
        };
    }
}
