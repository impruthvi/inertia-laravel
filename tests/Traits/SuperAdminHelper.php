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
        $adminRole = Role::create([
            'name' => Role::SUPER_ADMIN,
            'display_name' => Role::SUPER_ADMIN,
            'guard_name' => 'admin',
        ]);

        $defaultAdminPermissions = get_system_permissions(role_permissions('admin'));
        create_permissions($defaultAdminPermissions, $adminRole);

        $admin = Admin::factory()->create([
            'email' => 'superadmin@test.com',
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
            'created_by' => null,
            'updated_by' => null,
        ]);

        $admin->assignRole($adminRole);

        return $admin;
    }
}
