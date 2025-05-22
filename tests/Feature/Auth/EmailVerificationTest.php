<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;
    

    
    public function test_unverified_user_receives_verification_email()
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->postJson(route('verification.send'))
            ->assertOk()
            ->assertJson(['status' => 'verification-link-sent']);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    
    public function test_verified_user_does_not_receive_verification_email()
    {
        Notification::fake();

        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->postJson(route('verification.send'))
            ->assertOk()
            ->assertJson(['status' => 'verification-link-already']);

        Notification::assertNothingSent();
    }

    
    public function test_guest_user_cannot_request_verification_email()
    {
        $this->postJson(route('verification.send'))
            ->assertUnauthorized();
    }
}
