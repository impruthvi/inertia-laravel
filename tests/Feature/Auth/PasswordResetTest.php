<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }

    public function test_validation_exception_with_invalid_email(): void
    {

        // Attempt to reset password with an email that doesn't exist
        $response = $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => 'nonexistent@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        // Assert that the response is redirected back
        $response->assertStatus(302);

        // Assert that the response contains the validation error
        $response->assertSessionHasErrors([
            'email' => trans('passwords.user'),
        ]);
    }

    public function test_validation_exception_password_link_can_be_requested(): void
    {

        $response = $this->post('/forgot-password', ['email' => 'test@gmail.com']);

        // Assert that the response is redirected back
        $response->assertStatus(302);

        // Assert that the response contains the validation error
        $response->assertSessionHasErrors([
            'email' => trans('passwords.user'),
        ]);

    }
}
