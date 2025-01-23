<?php

namespace Tests\Feature\RoleManagement\Role;

use App\Enums\AdminRoleEnum;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ListRoleTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['name' => Role::SUPER_ADMIN, 'display_name' => Role::SUPER_ADMIN, 'guard_name' => 'admin']);
        $defaultAdminPermissions = get_system_permissions(role_permissions('admin'));
        create_permissions($defaultAdminPermissions, $adminRole);
    }

    public function test_admin_cant_see_roles_page_with_invalid_credentials(): void
    {
        $response = $this->get(route('admin.roles.index'));

        $response->assertFound()
            ->assertRedirectToRoute('login');
    }

    public function test_admin_can_see_role_page_with_valid_credentials(): void
    {
        $adminRole = Role::where('name', Role::SUPER_ADMIN)->first();

        $admin = Admin::factory()->create([
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
        ]);

        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.roles.index'));

        $response->assertOk();
    }


    public function test_admin_can_get_paginated_roles_data(): void
    {
        $adminRole = Role::where('name', Role::SUPER_ADMIN)->first();

        $admin = Admin::factory()->create([
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
        ]);

        $admin->assignRole($adminRole);

        Role::factory(20)->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.roles.index'));

        $response->assertInertia(fn(AssertableInertia $page) => $page->has('roles.data', 10));

        $this->assertDatabaseCount('roles', 21);
    }

    public function test_admin_can_get_role_data_with_paginated_url(): void
    {
        $adminRole = Role::where('name', Role::SUPER_ADMIN)->first();

        $admin = Admin::factory()->create([
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
        ]);

        $admin->assignRole($adminRole);

        Role::factory(20)->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.roles.index', ['page' => 2]));

        $response->assertInertia(fn(AssertableInertia $page) => $page->has('roles.data', 10));

        $this->assertDatabaseCount('roles', 21);
    }

    public function test_admin_can_search_role(): void
    {
        $adminRole = Role::where('name', Role::SUPER_ADMIN)->first();

        $admin = Admin::factory()->create([
            'role_id' => $adminRole->id,
            'role' => AdminRoleEnum::ADMIN->value,
        ]);

        $admin->assignRole($adminRole);

        Role::create([
            'name' => 'Super Admin',
            'display_name' => 'Super Admin',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Role::factory(20)->create();

        $getResponse = $this->actingAs($admin, 'admin')->get(route('admin.roles.index', [
            'search' => 'Admin',
            'sort' => [
                'display_name' => 'asc',
            ]
        ]));

        $getResponse->assertOk()
            ->assertInertia(fn(AssertableInertia $page) => $page->has('roles.data', 1));
    }
}
