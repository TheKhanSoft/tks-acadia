<?php

namespace App\Enums;

enum IsActiveFilter: string
{
    use EnumToSelectArray;

    case ALL = 'all';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::ALL => 'All Statuses',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }

    // public function label(): string
    // {
    //     return ucwords(strtolower(str_replace('_', ' ', $this->name)));
    // }

    // public static function toSelectArray(): array
    // {
    //     return collect(self::cases())->mapWithKeys(function ($case) {
    //         return [$case->value => $case->label()];
    //     })->all();
    // }

    public function getBoolValue(): ?bool
    {
        return match ($this) {
            self::ACTIVE => true,
            self::INACTIVE => false,
            self::ALL => null,
        };
    }
}
