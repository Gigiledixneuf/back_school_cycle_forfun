<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_a_password_reset_link_successfully()
    {
        $user = User::factory()->create(['email' => 'john@example.com']);

        $response = $this->postJson(route('password.email'), [
            'email' => 'john@example.com',
        ]);

        $response->assertOk()
                 ->assertJson([
                     'status' => trans(Password::RESET_LINK_SENT),
                 ]);
    }

    public function test_it_requires_email_field()
    {
        $response = $this->postJson(route('password.email'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_it_fails_with_non_existing_email()
    {
        $response = $this->postJson(route('password.email'), [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
