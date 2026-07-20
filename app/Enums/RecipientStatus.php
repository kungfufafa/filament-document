<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RecipientStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Viewed = 'viewed';
    case Signed = 'signed';
    case Declined = 'declined';
    case Waiting = 'waiting';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Needs to sign',
            self::Viewed => 'Viewed',
            self::Signed => 'Signed',
            self::Declined => 'Declined',
            self::Waiting => 'Waiting',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Viewed => 'info',
            self::Signed => 'success',
            self::Declined => 'danger',
            self::Waiting => 'gray',
        };
    }
}
