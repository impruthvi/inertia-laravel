<?php

declare(strict_types=1);

use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Models\Admin;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

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

test('clears rate limiter on successful login', function () {
    $admin = Admin::factory()->create();

    $request = new LoginRequest();

    $request->merge([
        'email' => $admin->email,
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
    $admin = Admin::factory()->create();
    Event::fake();

    $request = new LoginRequest();
    $request->merge([
        'email' => $admin->email,
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
