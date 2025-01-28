<?php

declare(strict_types=1);

namespace App\Enums;

enum AdminRoleEnum: string
{
    case ADMIN = 'admin';
    case SUPER_ADMIN = 'super_admin';
    case BUSINESS_OWNER = 'business_owner';
}
