<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\SuperAdminHelper;

use function Pest\Laravel\get;

uses(RefreshDatabase::class)->group('admin', 'roles');
uses(SuperAdminHelper::class);

beforeEach(function () {
    $this->superAdmin = $this->createSuperAdminAndLogin();
});

test('redirects to login when accessing admin create page without credentials', function () {
    get(route('admin.admins.create'))
        ->assertRedirectToRoute('login');
});

test('allows super admin to access the create admin page', function () {
    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.admins.create'))
        ->assertOk();
});

test('shows error when required fields are missing on admin create', function () {
    $this->actingAs($this->superAdmin, 'admin')
        ->post(route('admin.admins.store'), [])
        ->assertRedirect()
        ->assertSessionHasErrors(['first_name', 'last_name', 'email', 'role_id', 'custom_permission']);
});

test('successfully creates a admin with valid data', function () {
    $role = Role::factory()->create();

    $this->actingAs($this->superAdmin, 'admin')
        ->post(route('admin.admins.store'), [
            'first_name' => 'New Admin',
            'last_name' => 'Admin',
            'email' => 'newadmin@gmail.com',
            'role_id' => $role->id,
            'custom_permission' => [
                1 => ['add', 'edit', 'view', 'delete'],
            ],
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('messages.created', ['entity' => 'Admin']));

    expect(Admin::count())->toBe(2)
        ->and(Admin::where('first_name', 'New Admin')->exists())->toBeTrue();
});
