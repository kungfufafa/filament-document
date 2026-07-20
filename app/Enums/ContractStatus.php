<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ContractStatus: string implements HasColor, HasLabel
{
    case Draft = 'draft';
    case Active = 'active';
    case Expiring = 'expiring';
    case Expired = 'expired';
    case Renewed = 'renewed';
    case Terminated = 'terminated';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Active => 'Active',
            self::Expiring => 'Expiring soon',
            self::Expired => 'Expired',
            self::Renewed => 'Renewed',
            self::Terminated => 'Terminated',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Active => 'success',
            self::Expiring => 'warning',
            self::Expired => 'danger',
            self::Renewed => 'info',
            self::Terminated => 'gray',
        };
    }
}
