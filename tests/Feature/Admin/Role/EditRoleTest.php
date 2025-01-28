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
    $this->superAdmin = $this->createSuperAdminAndLogin();
});

function createTestRole($admin): Role
{
    actingAs($admin, 'admin')->post(
        route('admin.roles.store'),
        ['display_name' => 'Testing Role', 'roles' => [1 => ['add', 'edit', 'view', 'delete']]]
    );

    return Role::latest()->first();
}

test('redirects to login when accessing role edit page without credentials', function () {
    $this->get(route('admin.roles.edit', 1))
        ->assertFound()
        ->assertRedirectToRoute('login');
});

test('shows role not found when editing a non-existent role', function () {

    actingAs($this->superAdmin, 'admin')
        ->get(route('admin.roles.edit', 1111))
        ->assertFound()
        ->assertSessionHas('error', __('messages.not_found', ['entity' => 'Role']));
});

test('allows admin to access the role edit page with valid credentials', function () {

    $role = createTestRole($this->superAdmin);

    actingAs($this->superAdmin, 'admin')
        ->get(route('admin.roles.edit', $role->id))
        ->assertOk()
        ->assertInertia(
            fn (AssertableInertia $page) => $page->has(
                'role',
                fn ($roleData) => $roleData->where('id', $role->id)
                    ->where('display_name', $role->display_name)
                    ->etc()
            )
        );
});

test('updates role successfully with valid data', function () {

    $role = createTestRole($this->superAdmin);

    $updatedData = [
        'display_name' => 'Updated Testing Role',
        'roles' => [1 => ['add', 'edit', 'view', 'delete']],
    ];

    actingAs($this->superAdmin, 'admin')
        ->put(route('admin.roles.update', $role->id), $updatedData)
        ->assertFound()
        ->assertSessionHas('success', __('messages.updated', ['entity' => 'Role']))
        ->assertSessionDoesntHaveErrors();

    expect(Role::where('display_name', 'Updated Testing Role')->exists())->toBeTrue();
});

test('shows role not found when updating a non-existent role', function () {

    actingAs($this->superAdmin, 'admin')
        ->put(route('admin.roles.update', 1111), [
            'display_name' => 'Updated Testing Role',
            'roles' => [1 => ['add', 'edit', 'view', 'delete']],
        ])
        ->assertFound()
        ->assertSessionHas('error', __('messages.not_found', ['entity' => 'Role']));
});

test('deletes a role successfully', function () {

    $role = createTestRole($this->superAdmin);

    actingAs($this->superAdmin, 'admin')
        ->delete(route('admin.roles.destroy', $role->id))
        ->assertFound()
        ->assertSessionHas('success', __('messages.deleted', ['entity' => 'Role']))
        ->assertSessionDoesntHaveErrors();

    expect(Role::where('id', $role->id)->doesntExist())->toBeTrue();
});
