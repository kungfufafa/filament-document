<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SignatureMethod: string implements HasLabel
{
    case Type = 'type';
    case Draw = 'draw';
    case Upload = 'upload';

    public function getLabel(): string
    {
        return match ($this) {
            self::Type => 'Type',
            self::Draw => 'Draw',
            self::Upload => 'Upload',
        };
    }
}
