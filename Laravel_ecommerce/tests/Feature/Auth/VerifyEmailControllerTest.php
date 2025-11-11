<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\URL;

class VerifyEmailControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_verifies_the_user_email_successfully()
    {
        Event::fake();

        $user = User::factory()->unverified()->create();

        // Signed verification URL
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($url);

        $response->assertRedirect(route('dashboard', absolute: false) . '?verified=1');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

    /** @test */
    public function it_redirects_if_email_already_verified()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($url);

        $response->assertRedirect(route('dashboard', absolute: false) . '?verified=1');
    }

    /** @test */
    public function it_fails_if_signature_is_invalid()
    {
        $user = User::factory()->unverified()->create();

        $url = route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]);

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(403);
    }
}
