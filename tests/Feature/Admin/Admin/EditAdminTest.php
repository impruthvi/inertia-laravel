<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\Traits\SuperAdminHelper;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);
uses(SuperAdminHelper::class);

beforeEach(function () {
    $this->superAdmin = $this->createSuperAdminAndLogin();
});

function createTestAdmin($admin): Admin
{
    $role = Role::factory()->create();

    actingAs($admin, 'admin')->post(
        route('admin.admins.store'),
        [
            'first_name' => 'New Admin',
            'last_name' => 'Admin',
            'email' => 'newadmin@gmail.com',
            'role_id' => $role->id,
            'custom_permission' => [
                1 => ['add', 'edit', 'view', 'delete'],
            ],
        ]
    );

    return Admin::where('email', 'newadmin@gmail.com')->first();
}

test('redirects to login when accessing admin edit page without credentials', function () {
    $this->get(route('admin.admins.edit', 1))
        ->assertFound()
        ->assertRedirectToRoute('login');
});

test('shows admin not found when editing a non-existent role', function () {

    actingAs($this->superAdmin, 'admin')
        ->get(route('admin.admins.edit', 1111))
        ->assertFound()
        ->assertSessionHas('error', __('messages.not_found', ['entity' => 'Admin']));
});

test('allows admin to access the admin edit page with valid credentials', function () {

    $admin = createTestAdmin($this->superAdmin);

    actingAs($this->superAdmin, 'admin')
        ->get(route('admin.admins.edit', $admin->id))
        ->assertOk()
        ->assertInertia(
            fn (AssertableInertia $page) => $page->has(
                'admin',
                fn ($roleData) => $roleData->where('id', $admin->id)
                    ->where('first_name', 'New Admin')
                    ->etc()
            )
        );
});

test('allows admin to access the admin edit page with existing role', function () {

    $admin = createTestAdmin($this->superAdmin);
    $role = Role::factory()->create();

    actingAs($this->superAdmin, 'admin')
        ->get(route('admin.admins.edit', [$admin->id, 'role' => $role->id]))
        ->assertOk()
        ->assertInertia(
            fn (AssertableInertia $page) => $page->has(
                'admin',
                fn ($roleData) => $roleData->where('id', $admin->id)
                    ->where('first_name', 'New Admin')
                    ->etc()
            )
        );
});

test('try to update own permission', function () {

    $role = Role::factory()->create();

    $updatedData = [
        'first_name' => 'Updated Admin',
        'last_name' => 'Admin',
        'email' => 'asd@gmail.com',
        'role_id' => $role->id,
        'custom_permission' => [
            1 => ['add', 'edit', 'view', 'delete'],
        ],
    ];

    actingAs($this->superAdmin, 'admin')
        ->put(route('admin.admins.update', $this->superAdmin), $updatedData)
        ->assertFound()
        ->assertSessionHas('error', __('messages.cant_change_own'));

});

test('updates admin successfully with valid data', function () {

    $admin = createTestAdmin($this->superAdmin);
    $role = Role::factory()->create();

    $updatedData = [
        'first_name' => 'Updated Admin',
        'last_name' => 'Admin',
        'email' => 'asd@gmail.com',
        'role_id' => $role->id,
        'custom_permission' => [
            1 => ['add', 'edit', 'view', 'delete'],
        ],
    ];

    actingAs($this->superAdmin, 'admin')
        ->put(route('admin.admins.update', $admin->id), $updatedData)
        ->assertFound()
        ->assertFound()
        ->assertSessionHas('success', __('messages.updated', ['entity' => 'Admin']))
        ->assertSessionDoesntHaveErrors();

    expect(Admin::where('first_name', 'Updated Admin')->exists())->toBeTrue();
});

test('shows admin not found when updating a non-existent admin', function () {

    $role = Role::factory()->create();
    $updatedData = [
        'first_name' => 'Updated Admin',
        'last_name' => 'Admin',
        'email' => 'asd@gmail.com',
        'role_id' => $role->id,
        'custom_permission' => [
            1 => ['add', 'edit', 'view', 'delete'],
        ],
    ];

    actingAs($this->superAdmin, 'admin')
        ->put(route('admin.admins.update', 1111), $updatedData)
        ->assertFound()
        ->assertSessionHas('error', __('messages.not_found', ['entity' => 'Admin']));
});

test('deletes a admin successfully', function () {

    $admin = createTestAdmin($this->superAdmin);

    actingAs($this->superAdmin, 'admin')
        ->delete(route('admin.admins.destroy', $admin->id))
        ->assertFound()
        ->assertSessionHas('success', __('messages.deleted', ['entity' => 'Admin']))
        ->assertSessionDoesntHaveErrors();

    expect(Admin::where('first_name', 'New Admin')->doesntExist())->toBeTrue();
});
