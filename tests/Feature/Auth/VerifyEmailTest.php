<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifiedUserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_is_already_verified()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->getJson($url);

        $response->assertJson([
            'status' => 'verification-link-already',
        ]);
    }

    public function test_email_verification_success_sends_custom_notification()
    {
        $user = User::factory()->unverified()->create();
    
        $this->actingAs($user);
    
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
    
        $response = $this->getJson($url);
    
        $response->assertJson([
            'status' => 'verification-link-success',
        ]);
    
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
    
}
