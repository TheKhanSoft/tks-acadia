<?php

namespace App\Enums;

trait EnumToSelectArray
{
    public static function toSelectArray(): array
    {
        return collect(self::cases())
            ->map(fn($enum) => ['id' => $enum->value, 'name' => $enum->label()])
            ->all();
    }

    // public static function toSelectArray(): array
    // {
    //     return collect(self::cases())->mapWithKeys(function ($case) {
    //         return [$case->value => $case->label()];
    //     })->all();
    // }
}
