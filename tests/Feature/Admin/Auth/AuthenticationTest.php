<?php

declare(strict_types=1);

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('login screen can be rendered', function () {
    $this->get('/admin/login')
        ->assertOk();
});

test('admin can authenticate using the login screen', function () {
    $admin = Admin::factory()->create();

    $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'password',
    ])
        ->assertRedirect(route('admin.dashboard', absolute: false));

    $this->assertAuthenticated('admin');
});

test('admins cannot authenticate with invalid password', function () {
    $admin = Admin::factory()->create();

    $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest('admin');
});

test('admins can logout', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')
        ->post('/admin/logout')
        ->assertRedirect('/admin/login');

    $this->assertGuest('admin');
});
