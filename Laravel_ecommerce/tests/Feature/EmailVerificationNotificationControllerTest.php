<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\TestCase;

class EmailVerificationNotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function verified_user_is_redirected_to_dashboard()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertRedirect(route('dashboard')); // ✅ should redirect
        $response->assertSessionMissing('status'); // No verification email sent
    }

    /** @test */
    public function unverified_user_receives_verification_email()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertRedirect(); // back
        $response->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /** @test */
    public function unauthenticated_user_cannot_send_verification_email()
    {
        $response = $this->post('/email/verification-notification');

        $response->assertRedirect('/login'); // Laravel default
    }
}
