<?php

declare(strict_types=1);

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\Traits\SuperAdminHelper;

uses(RefreshDatabase::class);
uses(SuperAdminHelper::class);

beforeEach(function () {
    $this->superAdmin = $this->createSuperAdminAndLogin();
});

test('redirects to login when accessing admin page without credentials', function () {
    $this->get(route('admin.admins.index'))
        ->assertFound()
        ->assertRedirectToRoute('login');
});

test('displays admins page for admin with valid credentials', function () {

    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.admins.index'))
        ->assertOk();
});

test('paginates admin data correctly', function () {
    Admin::factory(20)->create([
        'created_by' => $this->superAdmin->id,
        'updated_by' => $this->superAdmin->id,
    ]);

    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.admins.index'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page->has('admins.data', 10) // Remaining 10 items
        );

    expect(Admin::where('created_by', $this->superAdmin->id)->count())->toBe(20); // 20 created + 1 super admin
});

test('displays admins on the second page with pagination', function () {
    Admin::factory(20)->create([
        'created_by' => $this->superAdmin->id,
        'updated_by' => $this->superAdmin->id,
    ]);

    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.admins.index', ['page' => 2]))
        ->assertInertia(
            fn (AssertableInertia $page) => $page->has('admins.data', 10) // Remaining 10 items
        );

    expect(Admin::where('created_by', $this->superAdmin->id)->count())->toBe(20); // 20 created
});

test('searches for admin correctly', function () {

    Admin::create([
        'first_name' => 'Super',
        'last_name' => 'Admin',
        'email' => 'admin@admin,com',
        'password' => 'password',
        'created_by' => $this->superAdmin->id,
        'updated_by' => $this->superAdmin->id,
    ]);

    Admin::factory(20)->create();

    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.admins.index', [
            'search' => 'Super',
            'sort' => [
                'name' => 'asc',
            ],
        ]))
        ->assertOk()
        ->assertInertia(
            fn (AssertableInertia $page) => $page->has('admins.data', 1) // Only one match for "Super Admin"
                ->where('admins.data.0.first_name', 'Super') // Verify match data
        );
});
