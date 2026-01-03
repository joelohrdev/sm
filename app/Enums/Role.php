<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case OWNER = 'owner';
    case COACH = 'coach';
    case GUARDIAN = 'guardian';
}
