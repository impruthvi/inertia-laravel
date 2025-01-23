<?php

// declare(strict_types=1);

// use App\Enums\AdminRoleEnum;
// use App\Models\Admin;
// use App\Models\Role;
// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Tests\Traits\SuperAdminHelper;
// use function Pest\Laravel\get;

// uses(SuperAdminHelper::class);
// uses(RefreshDatabase::class)->group('admin', 'users');

// beforeEach(function () {
//     $adminRole = Role::create([
//         'name' => Role::SUPER_ADMIN,
//         'display_name' => Role::SUPER_ADMIN,
//         'guard_name' => 'admin',
//     ]);
//     $defaultAdminPermissions = get_system_permissions(role_permissions('admin'));
//     create_permissions($defaultAdminPermissions, $adminRole);

    
// });


// test('redirects to login when accessing role create page without credentials', function () {
//     get(route('admin.users.create'))
//         ->assertRedirectToRoute('login');
// });

// test('allows super admin to access the create user page', function () {
//     $this->actingAs($this->superAdmin, 'admin')
//         ->get(route('admin.users.create'))
//         ->assertOk();
// });

// test('shows error when required fields are missing on role create', function () {
//     $this->actingAs($this->superAdmin, 'admin')
//         ->post(route('admin.users.store'), [])
//         ->assertRedirect()
//         ->assertSessionHasErrors(['name','email']);
// });

// test('successfully creates a role with valid data', function () {
//     $this->actingAs($this->superAdmin, 'admin')
//         ->post(route('admin.users.store'), [
//             'name' => 'Testing User',
//             'email' => 'test@test.com',
//         ])
//         ->assertRedirect()
//         ->assertSessionHas('success', __('messages.created', ['entity' => 'User']));

//     expect(Role::count())->toBe(2)
//         ->and(Role::where('name', 'Testing User')->exists())->toBeTrue();
// });
