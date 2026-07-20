<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DocumentStatus: string implements HasColor, HasLabel
{
    case Draft = 'draft';
    case NeedsToSign = 'needs_to_sign';
    case Pending = 'pending';
    case Completed = 'completed';
    case Voided = 'voided';
    case Declined = 'declined';
    case Trash = 'trash';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::NeedsToSign => 'Needs to sign',
            self::Pending => 'Pending document',
            self::Completed => 'Completed',
            self::Voided => 'Voided',
            self::Declined => 'Declined',
            self::Trash => 'Trash',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::NeedsToSign => 'warning',
            self::Pending => 'info',
            self::Completed => 'success',
            self::Voided => 'danger',
            self::Declined => 'danger',
            self::Trash => 'gray',
        };
    }
}
