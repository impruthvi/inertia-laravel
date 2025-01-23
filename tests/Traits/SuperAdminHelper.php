<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Enums\AdminRoleEnum;
use App\Models\Admin;
use App\Models\Role;

trait SuperAdminHelper
{
    public function createSuperAdminAndLogin()
    {
        $adminRole = Role::where('name', Role::SUPER_ADMIN)->first();
        $admin = Admin::factory()->create([
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
        ]);
        $admin->assignRole($adminRole);

        return $admin;
    }
}
