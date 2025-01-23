<?php

declare(strict_types=1);

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\Traits\SuperAdminHelper;

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

test('redirects to login when accessing roles page without credentials', function () {
    $this->get(route('admin.roles.index'))
        ->assertFound()
        ->assertRedirectToRoute('login');
});

test('displays roles page for admin with valid credentials', function () {
    $admin = $this->createSuperAdminAndLogin();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.roles.index'))
        ->assertOk();
});

test('paginates roles data correctly', function () {
    $admin = $this->createSuperAdminAndLogin();
    Role::factory(20)->create();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.roles.index'))
        ->assertInertia(fn (AssertableInertia $page) => 
            $page->has('roles.data', 10) // Default page size
        );

    expect(Role::count())->toBe(21); // 20 created + 1 initial super admin role
});

test('displays roles on the second page with pagination', function () {
    $admin = $this->createSuperAdminAndLogin();
    Role::factory(20)->create();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.roles.index', ['page' => 2]))
        ->assertInertia(fn (AssertableInertia $page) => 
            $page->has('roles.data', 10) // Remaining 10 items
        );

    expect(Role::count())->toBe(21);
});

test('searches for roles correctly', function () {
    $admin = $this->createSuperAdminAndLogin();

    Role::create([
        'name' => 'Super Admin',
        'display_name' => 'Super Admin',
        'created_by' => $admin->id,
        'updated_by' => $admin->id,
    ]);

    Role::factory(20)->create();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.roles.index', [
            'search' => 'Super',
            'sort' => [
                'display_name' => 'asc',
            ],
        ]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => 
            $page->has('roles.data', 1) // Only one match for "Super Admin"
                ->where('roles.data.0.display_name', 'Super Admin') // Verify match data
        );
});
