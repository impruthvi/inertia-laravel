<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('renders confirm password screen', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/confirm-password')
        ->assertOk();
});

test('confirms password successfully', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/confirm-password', [
            'password' => 'password',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();
});

test('fails to confirm password with invalid password', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/confirm-password', [
            'password' => 'wrong-password',
        ])
        ->assertSessionHasErrors('password');
});
