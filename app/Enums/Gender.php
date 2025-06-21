<?php

namespace App\Enums;

enum Gender: string
{
    use EnumToSelectArray;

    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';

    public function label(): string
    {
        return ucwords(strtolower(str_replace('_', ' ', $this->name)));
    }
}
