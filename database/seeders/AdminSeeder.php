<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AdminRoleEnum;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => Str::uuid(), 'display_name' => Role::SUPER_ADMIN, 'guard_name' => 'admin']);
        $defaultAdminPermissions = get_system_permissions(role_permissions('admin'));
        create_permissions($defaultAdminPermissions, $adminRole, 'admin');

        $admin = Admin::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'Admin',
            'email' => 'admin@test.com',
            'role' => AdminRoleEnum::SUPER_ADMIN->value,
            'password' => bcrypt('admin-123'),
            'role_id' => $adminRole->id,
        ]);
        $admin->assignRole($adminRole);
    }
}
