<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestRoutesRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_is_redirected_from_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/login')
            ->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_authenticated_user_is_redirected_from_register(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/register')
            ->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_authenticated_user_is_redirected_from_forgot_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/forgot-password')
            ->assertRedirect(route('dashboard', absolute: false));
    }
}
