<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ApprovalType: string implements HasLabel
{
    case WithDocument = 'with_document';
    case WithoutDocument = 'without_document';
    case Form = 'form';

    public function getLabel(): string
    {
        return match ($this) {
            self::WithDocument => 'With document',
            self::WithoutDocument => 'Without document',
            self::Form => 'Form',
        };
    }
}
