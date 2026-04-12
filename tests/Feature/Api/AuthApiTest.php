<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_login_returns_unified_json_and_authenticates(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonPath('is_success', true)
            ->assertJsonPath('errors', null)
            ->assertJsonPath('data.user.email', $user->email)
            ->assertJsonStructure([
                'meta' => ['current_page', 'last_page', 'per_page', 'total', 'count'],
            ]);

        $this->assertAuthenticated();
    }

    public function test_login_failure_returns_401_unified_shape(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong',
        ]);

        $response->assertUnauthorized()
            ->assertJsonPath('is_success', false)
            ->assertJsonPath('errors.email', fn ($v) => is_string($v) && $v !== '');

        $this->assertGuest();
    }

    public function test_login_validation_uses_api_response_shape(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'not-an-email',
            'password' => '',
        ]);

        $response->assertUnprocessable()
            ->assertJsonPath('is_success', false)
            ->assertJsonStructure(['errors' => ['email', 'password']]);
    }

    public function test_register_returns_201_and_unified_shape(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Api User',
            'email' => 'api@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertCreated()
            ->assertJsonPath('is_success', true)
            ->assertJsonPath('data.user.email', 'api@example.com');

        $this->assertAuthenticated();
    }

    public function test_me_requires_authentication_and_returns_user(): void
    {
        $this->getJson('/api/auth/me')->assertUnauthorized()
            ->assertJsonPath('is_success', false);

        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson('/api/auth/me');

        $response->assertOk()
            ->assertJsonPath('is_success', true)
            ->assertJsonPath('data.user.id', $user->id);
    }

    public function test_logout_returns_success_and_guest(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/auth/logout');

        $response->assertOk()
            ->assertJsonPath('is_success', true);

        $this->assertGuest();
    }

    public function test_forgot_password_returns_success_shape(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertOk()
            ->assertJsonPath('is_success', true);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_returns_success_shape(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->postJson('/api/auth/forgot-password', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $response = $this->postJson('/api/auth/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ]);

            $response->assertOk()
                ->assertJsonPath('is_success', true)
                ->assertJsonPath('errors', null);

            return true;
        });
    }
}
