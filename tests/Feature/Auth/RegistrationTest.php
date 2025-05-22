<?php

namespace Tests\Feature\Auth;

use App\Events\UserRegisteredEvent;
use Tests\TestCase;
use App\Models\User;
use App\Events\RegisteredUserEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    
    public function test_registers_a_user_successfully()
    {
        Event::fake();

        $response = $this->postJson(route('register'), [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'passwordtest',
            'password_confirmation' => 'passwordtest',
        ]);

        $response->assertNoContent();
        $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);

        Event::assertDispatched(UserRegisteredEvent::class);
    }

    
    public function test_requires_name_email_and_password()
    {
        $response = $this->postJson(route('register'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    
    public function test_requires_valid_email()
    {
        $response = $this->postJson(route('register'), [
            'name' => 'Jane',
            'email' => 'invalid-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'tutor',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    
    public function test_requires_password_confirmation()
    {
        $response = $this->postJson(route('register'), [
            'name' => 'Jane',
            'email' => 'jane@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    
    public function test_requires_unique_email()
    {
        User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $response = $this->postJson(route('register'), [
            'name' => 'New User',
            'email' => 'duplicate@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
