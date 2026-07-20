<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FieldType: string implements HasLabel
{
    case Signature = 'signature';
    case Initials = 'initials';
    case Date = 'date';
    case Text = 'text';
    case Stamp = 'stamp';
    case Emeterai = 'emeterai';

    public function getLabel(): string
    {
        return match ($this) {
            self::Signature => 'Signature',
            self::Initials => 'Initials',
            self::Date => 'Date',
            self::Text => 'Text',
            self::Stamp => 'Corporate stamp',
            self::Emeterai => 'eMeterai',
        };
    }
}
