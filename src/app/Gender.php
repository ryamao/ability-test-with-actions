<?php

declare(strict_types=1);

namespace App;

enum Gender: int
{
    case Male = 1;
    case Female = 2;
    case Other = 3;

    public function name(): string
    {
        return match ($this->value) {
            1 => '男性',
            2 => '女性',
            3 => 'その他',
        };
    }
}
