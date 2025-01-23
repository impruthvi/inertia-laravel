<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('can render reset password link screen', function () {
    $this->get('/forgot-password')
        ->assertOk();
});

test('can request reset password link', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('can render reset password screen with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $this->get('/reset-password/'.$notification->token)
            ->assertOk();

        return true;
    });
});

test('can reset password with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        return true;
    });
});

test('validation error for invalid email during reset password', function () {
    $response = $this->post('/reset-password', [
        'token' => 'invalid-token',
        'email' => 'nonexistent@example.com',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors([
            'email' => trans('passwords.user'),
        ]);
});

test('validation error for invalid email during reset link request', function () {
    $response = $this->post('/forgot-password', ['email' => 'test@gmail.com']);

    $response->assertRedirect()
        ->assertSessionHasErrors([
            'email' => trans('passwords.user'),
        ]);
});
