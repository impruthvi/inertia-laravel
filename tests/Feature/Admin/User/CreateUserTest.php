<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\SuperAdminHelper;

uses(SuperAdminHelper::class);
uses(RefreshDatabase::class)->group('admin', 'user');

beforeEach(function () {
    $this->superAdmin = $this->createSuperAdminAndLogin();
});

test('shows error when required fields are missing on user create', function () {
    $this->actingAs($this->superAdmin, 'admin')
        ->post(route('admin.users.store'), [])
        ->assertRedirect()
        ->assertSessionHasErrors(['name', 'email']);
});

test('successfully creates a user with valid data', function () {
    $this->actingAs($this->superAdmin, 'admin')
        ->post(route('admin.users.store'), [
            'name' => 'Testing User',
            'email' => 'test@test.com',
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('messages.created', ['entity' => 'User']));

    expect(User::count())->toBe(1)
        ->and(User::where('name', 'Testing User')->exists())->toBeTrue();
});
