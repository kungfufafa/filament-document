<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum NavigationGroup implements HasIcon, HasLabel
{
    case Documents;
    case Approvals;
    case Contracts;
    case Templates;
    case Emeterai;
    case Contacts;
    case Settings;

    public function getLabel(): string
    {
        return match ($this) {
            self::Documents => 'Documents',
            self::Approvals => 'Approvals',
            self::Contracts => 'Contracts',
            self::Templates => 'Templates',
            self::Emeterai => 'eMeterai',
            self::Contacts => 'Contacts',
            self::Settings => 'Settings',
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Documents => Heroicon::OutlinedDocumentText,
            self::Approvals => Heroicon::OutlinedClipboardDocumentCheck,
            self::Contracts => Heroicon::OutlinedDocumentDuplicate,
            self::Templates => Heroicon::OutlinedRectangleStack,
            self::Emeterai => Heroicon::OutlinedQrCode,
            self::Contacts => Heroicon::OutlinedUsers,
            self::Settings => Heroicon::OutlinedCog6Tooth,
        };
    }
}
