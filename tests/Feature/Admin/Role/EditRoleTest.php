<?php

declare(strict_types=1);

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\Traits\SuperAdminHelper;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);
uses(SuperAdminHelper::class);

beforeEach(function () {
    $adminRole = Role::create([
        'name' => Role::SUPER_ADMIN,
        'display_name' => Role::SUPER_ADMIN,
        'guard_name' => 'admin',
    ]);
    $defaultAdminPermissions = get_system_permissions(role_permissions('admin'));
    create_permissions($defaultAdminPermissions, $adminRole);
});

function createTestRole($admin): Role
{
    actingAs($admin, 'admin')->post(
        route('admin.roles.store'),
        ['role_display_name' => 'Testing Role', 'roles' => [1 => ['add', 'edit', 'view', 'delete']]]
    );

    return Role::latest()->first();
}

test('redirects to login when accessing role edit page without credentials', function () {
    $this->get(route('admin.roles.edit', 1))
        ->assertFound()
        ->assertRedirectToRoute('login');
});

test('shows role not found when editing a non-existent role', function () {
    $admin = $this->createSuperAdminAndLogin();

    actingAs($admin, 'admin')
        ->get(route('admin.roles.edit', 1111))
        ->assertFound()
        ->assertSessionHas('error', __('messages.not_found', ['entity' => 'Role']));
});

test('allows admin to access the role edit page with valid credentials', function () {
    $admin = $this->createSuperAdminAndLogin();
    $role = createTestRole($admin);

    actingAs($admin, 'admin')
        ->get(route('admin.roles.edit', $role->id))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => 
            $page->has('role', fn ($roleData) => 
                $roleData->where('id', $role->id)
                         ->where('display_name', 'super_admin')
                         ->etc()
            )
        );
});

test('updates role successfully with valid data', function () {
    $admin = $this->createSuperAdminAndLogin();
    $role = createTestRole($admin);

    $updatedData = [
        'display_name' => 'Updated Testing Role',
        'roles' => [1 => ['add', 'edit', 'view', 'delete']],
    ];

    actingAs($admin, 'admin')
        ->put(route('admin.roles.update', $role->id), $updatedData)
        ->assertFound()
        ->assertSessionHas('success', __('messages.updated', ['entity' => 'Role']))
        ->assertSessionDoesntHaveErrors();

    expect(Role::where('display_name', 'Updated Testing Role')->exists())->toBeTrue();
});

test('shows role not found when updating a non-existent role', function () {
    $admin = $this->createSuperAdminAndLogin();

    actingAs($admin, 'admin')
        ->put(route('admin.roles.update', 1111), [
            'display_name' => 'Updated Testing Role',
            'roles' => [1 => ['add', 'edit', 'view', 'delete']],
        ])
        ->assertFound()
        ->assertSessionHas('error', __('messages.not_found', ['entity' => 'Role']));
});

test('deletes a role successfully', function () {
    $admin = $this->createSuperAdminAndLogin();
    $role = createTestRole($admin);

    actingAs($admin, 'admin')
        ->delete(route('admin.roles.destroy', $role->id))
        ->assertFound()
        ->assertSessionHas('success', __('messages.deleted', ['entity' => 'Role']))
        ->assertSessionDoesntHaveErrors();

    expect(Role::where('display_name', 'Testing Role')->doesntExist())->toBeTrue();
});
