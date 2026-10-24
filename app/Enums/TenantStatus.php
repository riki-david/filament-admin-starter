<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TenantStatus: string implements HasLabel, HasColor, HasIcon
{
    case Active    = 'active';
    case Suspended = 'suspended';

    public function getLabel(): string
    {
        return match ($this) {
            self::Active    => 'Active',
            self::Suspended => 'Suspended',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Active    => 'success',
            self::Suspended => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Active    => 'heroicon-m-check-circle',
            self::Suspended => 'heroicon-m-pause-circle',
        };
    }
}
