<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('auth');

it('renders the registration screen', function () {
    $this->get('/register')
        ->assertOk(); // Ensure that the registration page is accessible
});

it('allows new users to register successfully', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertRedirect(route('dashboard', absolute: false)); // Check if the user is redirected to the dashboard after registration

    expect(auth()->check())->toBeTrue(); // Confirm that the user is authenticated
});
