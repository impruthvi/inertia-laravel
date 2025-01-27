<?php

declare(strict_types=1);

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('renders login screen', function () {
    $this->get('/login')
        ->assertOk();
});

test('authenticates users with valid credentials', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

test('prevents authentication with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])
        ->assertSessionHasErrors('email'); // Optional: Check for error feedback

    $this->assertGuest();
});

test('logs out authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect('/');

    $this->assertGuest();
});

test('clears rate limiter on successful login', function () {
    $user = User::factory()->create();

    $request = new LoginRequest();

    $request->merge([
        'email' => $user->email,
        'password' => 'password',
    ]);

    RateLimiter::hit($request->throttleKey());

    // check rate limiter
    expect(RateLimiter::attempts($request->throttleKey()))->toBe(1);

    $request->authenticate();

    // check rate limiter
    expect(RateLimiter::attempts($request->throttleKey()))->toBe(0);
});

test('blocks access after 5 failed attempts', function () {
    $user = User::factory()->create();
    Event::fake();

    $request = new LoginRequest();
    $request->merge([
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    // Simulate 5 failed login attempts
    for ($i = 0; $i < 5; $i++) {
        try {
            $request->authenticate();
        } catch (ValidationException $e) {
            // Expected exception
        }
    }

    // Try sixth attempt
    try {
        $request->authenticate();
        $this->fail('Expected rate limit exception was not thrown');
    } catch (ValidationException $e) {
        expect($e->errors())
            ->toHaveKey('email')
            ->and(RateLimiter::attempts($request->throttleKey()))->toBe(5)
            ->and(RateLimiter::tooManyAttempts($request->throttleKey(), 5))->toBeTrue();
    }

    Event::assertDispatched(Lockout::class);
});
