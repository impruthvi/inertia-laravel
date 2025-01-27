<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\SuperAdminHelper;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);
uses(SuperAdminHelper::class);

beforeEach(function () {
    $this->superAdmin = $this->createSuperAdminAndLogin();
});

function createTestUser($admin): User
{
    actingAs($admin, 'admin')->post(
        route('admin.users.store'),
        ['name' => 'Testing User', 'email' => 'test@test.com']
    );

    return User::latest()->first();
}

test('redirects to login when accessing user edit page without credentials', function () {
    $this->get(route('admin.users.edit', 1))
        ->assertFound()
        ->assertRedirectToRoute('login');
});

test('shows user not found when editing a non-existent user', function () {

    actingAs($this->superAdmin, 'admin')
        ->get(route('admin.users.edit', 1111))
        ->assertExactJson(
            [
                'message' => trans('messages.not_found', ['entity' => 'User']),
                'error' => true,
            ]
        );
});

test('allows admin to access the user edit page with valid credentials', function () {

    $user = createTestUser($this->superAdmin);

    actingAs($this->superAdmin, 'admin')
        ->get(route('admin.users.edit', $user->id))
        ->assertOk()
        ->assertExactJson([
            'error' => false,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ],
        ]);
});

test('updates user successfully with valid data', function () {

    $user = createTestUser($this->superAdmin);

    $updatedData = [
        'name' => 'Updated Testing User',
        'email' => 'updated@user.com',
    ];

    actingAs($this->superAdmin, 'admin')
        ->put(route('admin.users.update', $user->id), $updatedData)
        ->assertFound()
        ->assertSessionHas('success', __('messages.updated', ['entity' => 'User']))
        ->assertSessionDoesntHaveErrors();

    expect(User::where('name', 'Updated Testing User')->exists())->toBeTrue();
});

test('shows user not found when updating a non-existent user', function () {

    actingAs($this->superAdmin, 'admin')
        ->put(route('admin.users.update', 1111), [
            'name' => 'Updated Testing User',
            'email' => 'updated@user.com',
        ])
        ->assertFound()
        ->assertSessionHas('error', __('messages.not_found', ['entity' => 'User']));
});

test('deletes a user successfully', function () {

    $user = createTestUser($this->superAdmin);

    actingAs($this->superAdmin, 'admin')
        ->delete(route('admin.users.destroy', $user->id))
        ->assertFound()
        ->assertSessionHas('success', __('messages.deleted', ['entity' => 'User']))
        ->assertSessionDoesntHaveErrors();

    expect(User::where('name', 'Testing User')->doesntExist())->toBeTrue();
});
