<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DocumentActivityType: string implements HasLabel
{
    case Created = 'created';
    case Sent = 'sent';
    case Viewed = 'viewed';
    case Signed = 'signed';
    case Completed = 'completed';
    case Declined = 'declined';
    case Voided = 'voided';
    case Resent = 'resent';
    case Downloaded = 'downloaded';
    case Moved = 'moved';
    case Shared = 'shared';

    public function getLabel(): string
    {
        return match ($this) {
            self::Created => 'Created',
            self::Sent => 'Sent',
            self::Viewed => 'Viewed',
            self::Signed => 'Signed',
            self::Completed => 'Completed',
            self::Declined => 'Declined',
            self::Voided => 'Voided',
            self::Resent => 'Resent',
            self::Downloaded => 'Downloaded',
            self::Moved => 'Moved',
            self::Shared => 'Shared',
        };
    }
}
