<?php

declare(strict_types=1);

namespace Tests\Feature\RoleManagement\Role;

use App\Enums\AdminRoleEnum;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

final class EditRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['name' => Role::SUPER_ADMIN, 'display_name' => Role::SUPER_ADMIN, 'guard_name' => 'admin']);
        $defaultAdminPermissions = get_system_permissions(role_permissions('admin'));
        create_permissions($defaultAdminPermissions, $adminRole);
    }

    public function test_admin_cant_see_role_edit_page_with_invalid_credentials(): void
    {
        $response = $this->get(route('admin.roles.edit', 1));

        $response->assertFound()
            ->assertRedirectToRoute('login');
    }

    public function test_admin_cant_edit_and_get_role_not_found_for_non_existed_role(): void
    {
        $adminRole = Role::where('name', Role::SUPER_ADMIN)->first();

        $admin = Admin::factory()->create([
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
        ]);

        $admin = Admin::factory()->create([
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
        ]);

        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.roles.edit', 1111));

        $response->assertFound()
            ->assertSessionHas('error', __('messages.not_found', ['entity' => 'Role']));
    }

    public function test_admin_can_see_role_edit_page_with_valid_credentials(): void
    {
        $adminRole = Role::where('name', Role::SUPER_ADMIN)->first();

        $admin = Admin::factory()->create([
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
        ]);

        $admin->assignRole($adminRole);

        $this->actingAs($admin, 'admin')->post(
            route('admin.roles.store'),
            ['role_display_name' => 'Testing Role', 'roles' => [1 => ['add', 'edit', 'view', 'delete']]]
        );

        $role = Role::latest()->first();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.roles.edit', $role->id));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->has('role'));
    }

    public function test_admin_can_edit_role(): void
    {
        $adminRole = Role::where('name', Role::SUPER_ADMIN)->first();

        $admin = Admin::factory()->create([
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
        ]);

        $admin->assignRole($adminRole);

        $this->actingAs($admin, 'admin')->post(
            route('admin.roles.store'),
            ['role_display_name' => 'Testing Role', 'roles' => [1 => ['add', 'edit', 'view', 'delete']]]
        );

        $role = Role::latest()->first();

        $updatedData = [
            'display_name' => 'Updated Testing Role',
            'roles' => [1 => ['add', 'edit', 'view', 'delete']],
        ];

        $response = $this->actingAs($admin, 'admin')->put(route('admin.roles.update', $role->id), $updatedData);

        $response->assertFound()
            ->assertSessionHas('success', __('messages.updated', ['entity' => 'Role']))
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('roles', ['display_name' => 'Updated Testing Role']);
    }

    // role not found
    public function test_role_not_found()
    {
        $adminRole = Role::where('name', Role::SUPER_ADMIN)->first();

        $admin = Admin::factory()->create([
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
        ]);

        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin, 'admin')->put(route('admin.roles.update', 1111), [
            'display_name' => 'Updated Testing Role',
            'roles' => [1 => ['add', 'edit', 'view', 'delete']],
        ]);

        $response->assertFound()
            ->assertSessionHas('error', __('messages.not_found', ['entity' => 'Role']));
    }

    // delete role
    public function test_admin_can_delete_role()
    {
        $adminRole = Role::where('name', Role::SUPER_ADMIN)->first();

        $admin = Admin::factory()->create([
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
        ]);

        $admin->assignRole($adminRole);

        $this->actingAs($admin, 'admin')->post(
            route('admin.roles.store'),
            ['role_display_name' => 'Testing Role', 'roles' => [1 => ['add', 'edit', 'view', 'delete']]]
        );

        $role = Role::latest()->first();

        $response = $this->actingAs($admin, 'admin')->delete(route('admin.roles.destroy', $role->id));

        $response->assertFound()
            ->assertSessionHas('success', __('messages.deleted', ['entity' => 'Role']))
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseMissing('roles', ['display_name' => 'Testing Role']);
    }
}
