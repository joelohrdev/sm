<?php

declare(strict_types=1);

namespace App\Enums;

enum Bats: string
{
    case RIGHT = 'right';
    case LEFT = 'left';
    case SWITCH = 'switch';
}
