<?php

declare(strict_types=1);

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\SuperAdminHelper;

use function Pest\Laravel\get;

uses(RefreshDatabase::class)->group('admin', 'roles');
uses(SuperAdminHelper::class);

beforeEach(function () {
    $this->superAdmin = $this->createSuperAdminAndLogin();
});

test('redirects to login when accessing role create page without credentials', function () {
    get(route('admin.roles.create'))
        ->assertRedirectToRoute('login');
});

test('allows super admin to access the create role page', function () {
    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.roles.create'))
        ->assertOk();
});

test('shows error when required fields are missing on role create', function () {
    $this->actingAs($this->superAdmin, 'admin')
        ->post(route('admin.roles.store'), [])
        ->assertRedirect()
        ->assertSessionHasErrors(['display_name']);
});

test('successfully creates a role with valid data', function () {
    $this->actingAs($this->superAdmin, 'admin')
        ->post(route('admin.roles.store'), [
            'display_name' => 'Testing Role',
            'roles' => [1 => ['add', 'edit', 'view', 'delete']],
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('messages.created', ['entity' => 'Role']));

    expect(Role::count())->toBe(2)
        ->and(Role::where('display_name', 'Testing Role')->exists())->toBeTrue();
});
