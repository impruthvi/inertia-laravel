<?php

declare(strict_types=1);

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\Traits\SuperAdminHelper;

uses(RefreshDatabase::class);
uses(SuperAdminHelper::class);

beforeEach(function () {
    $this->superAdmin = $this->createSuperAdminAndLogin();
});

test('redirects to login when accessing roles page without credentials', function () {
    $this->get(route('admin.roles.index'))
        ->assertFound()
        ->assertRedirectToRoute('login');
});

test('displays roles page for admin with valid credentials', function () {

    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.roles.index'))
        ->assertOk();
});

test('paginates roles data correctly', function () {
    Role::factory(20)->create();

    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.roles.index'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page->has('roles.data', 10) // Default page size
        );

    expect(Role::count())->toBe(21); // 20 created + 1 initial super admin role
});

test('displays roles on the second page with pagination', function () {
    Role::factory(20)->create();

    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.roles.index', ['page' => 2]))
        ->assertInertia(
            fn (AssertableInertia $page) => $page->has('roles.data', 10) // Remaining 10 items
        );

    expect(Role::count())->toBe(21);
});

test('searches for roles correctly', function () {

    Role::create([
        'name' => 'Super Admin',
        'display_name' => 'Super Admin',
        'created_by' => $this->superAdmin->id,
        'updated_by' => $this->superAdmin->id,
    ]);

    Role::factory(20)->create();

    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.roles.index', [
            'search' => 'Super',
            'sort' => [
                'display_name' => 'asc',
            ],
        ]))
        ->assertOk()
        ->assertInertia(
            fn (AssertableInertia $page) => $page->has('roles.data', 1) // Only one match for "Super Admin"
                ->where('roles.data.0.display_name', 'Super Admin') // Verify match data
        );
});
