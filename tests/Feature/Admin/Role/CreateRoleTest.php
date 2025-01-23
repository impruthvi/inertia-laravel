<?php

declare(strict_types=1);

namespace Tests\Feature\RoleManagement\Role;

use App\Enums\AdminRoleEnum;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cant_see_role_create_page_with_invalid_credentials(): void
    {
        $response = $this->get(route('admin.roles.create'));

        $response->assertFound()
            ->assertRedirectToRoute('login');
    }

    public function test_admin_can_see_create_role_page_with_valid_credentials(): void
    {
        $adminRole = Role::create(['name' => Role::SUPER_ADMIN, 'display_name' => Role::SUPER_ADMIN, 'guard_name' => 'admin']);
        $defaultAdminPermissions = get_system_permissions(role_permissions('admin'));
        create_permissions($defaultAdminPermissions, $adminRole);

        $admin = Admin::factory()->create([
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
        ]);

        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.roles.create'));

        $response->assertOk();
    }

    public function test_admin_gets_empty_field_error_on_role_create(): void
    {
        $adminRole = Role::create(['name' => Role::SUPER_ADMIN, 'display_name' => Role::SUPER_ADMIN, 'guard_name' => 'admin']);
        $defaultAdminPermissions = get_system_permissions(role_permissions('admin'));
        create_permissions($defaultAdminPermissions, $adminRole);

        $admin = Admin::factory()->create([
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
        ]);

        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.roles.store'), []);

        $response->assertFound()
            ->assertSessionHasErrors(['display_name']);
    }

    public function test_admin_can_successfully_create_role(): void
    {
        $adminRole = Role::create(['name' => Role::SUPER_ADMIN, 'display_name' => Role::SUPER_ADMIN, 'guard_name' => 'admin']);
        $defaultAdminPermissions = get_system_permissions(role_permissions('admin'));
        create_permissions($defaultAdminPermissions, $adminRole);

        $admin = Admin::factory()->create([
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
        ]);

        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin, 'admin')->post(
            route('admin.roles.store'),
            ['display_name' => 'Testing Role', 'roles' => [1 => ['add', 'edit', 'view', 'delete']]]
        );

        $response->assertFound()
            ->assertSessionHas('success', __('messages.created', ['entity' => 'Role']))
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseCount('roles', 2)
            ->assertDatabaseHas('roles', ['display_name' => 'Testing Role']);
    }
}
