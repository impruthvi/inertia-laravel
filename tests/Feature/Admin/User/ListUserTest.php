<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\Traits\SuperAdminHelper;

uses(RefreshDatabase::class);
uses(SuperAdminHelper::class);

beforeEach(function () {
    $this->superAdmin = $this->createSuperAdminAndLogin();
});

test('redirects to login when accessing users page without credentials', function () {
    $this->get(route('admin.users.index'))
        ->assertFound()
        ->assertRedirectToRoute('login');
});

test('displays users page for admin with valid credentials', function () {

    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.users.index'))
        ->assertOk();
});

test('paginates users data correctly', function () {
    User::factory(20)->create();

    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.users.index'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page->has('users.data', 10) // Default page size
        );

    expect(User::count())->toBe(20); // 20 created
});

test('displays users on the second page with pagination', function () {
    User::factory(20)->create();

    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.users.index', ['page' => 2]))
        ->assertInertia(
            fn (AssertableInertia $page) => $page->has('users.data', 10) // Remaining 10 items
        );

    expect(User::count())->toBe(20);
});

test('searches for users correctly', function () {

    User::create([
        'name' => 'Test User',
        'email' => 'test@user.com',
        'password' => 'password',
    ]);

    User::factory(20)->create();

    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.users.index', [
            'search' => 'Test',
            'sort' => [
                'name' => 'asc',
            ],
        ]))
        ->assertOk()
        ->assertInertia(
            fn (AssertableInertia $page) => $page->has('users.data', 1) // Only one match for "Test User"
                ->where('users.data.0.name', 'Test User') // Verify match data
        );
});
