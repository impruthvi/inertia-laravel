<?php

declare(strict_types=1);

namespace App\Enums;

enum AdminRoleEnum: string
{
    case ADMIN = 'admin';
    case BUSINESS_OWNER = 'business_owner';
}
