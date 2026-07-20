<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ContactVisibility: string implements HasColor, HasLabel
{
    case Personal = 'personal';
    case Company = 'company';

    public function getLabel(): string
    {
        return match ($this) {
            self::Personal => 'Personal',
            self::Company => 'Company',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Personal => 'gray',
            self::Company => 'info',
        };
    }
}
