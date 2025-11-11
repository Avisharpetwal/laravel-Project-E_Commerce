<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Test user create
        $this->user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);
    }

    
    public function test_authenticated_user_can_view_profile_page()
    {
        $response = $this->actingAs($this->user)->get(route('profile.show'));

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee($this->user->email);
    }

    /** @test */
    public function test_authenticated_user_can_update_profile()
    {
        $newData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->actingAs($this->user)->post(route('profile.update'), $newData);

        // Check redirect to login after logout
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Profile updated successfully.');

        // Verify changes in DB
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        // Verify password updated
        $this->assertTrue(Hash::check('newpassword', $this->user->fresh()->password));
    }
}
