<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
    }

    public function test_authenticated_user_can_view_profile_page()
    {
        $response = $this->actingAs($this->user)->get('/profile');

        $response->assertStatus(200);
        $response->assertViewIs('profile.show'); // ✅ updated
        $response->assertViewHas('user', function ($viewUser) {
            return $viewUser->id === $this->user->id;
        });
    }

    public function test_user_can_update_their_name_without_password()
    {
        $response = $this->actingAs($this->user)->post('/profile', [
            'name' => 'Updated User',
            'email' => $this->user->email, // ✅ added
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Profile updated successfully.');

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated User',
        ]);
    }

    public function test_user_can_update_password_successfully()
    {
        $response = $this->actingAs($this->user)->post('/profile', [
            'name' => $this->user->name,
            'email' => $this->user->email, // ✅ added
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Profile updated successfully.');

        $this->user->refresh();
        $this->assertTrue(Hash::check('newpassword', $this->user->password));
    }

    public function test_unauthenticated_user_cannot_access_profile_page()
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_cannot_update_profile()
    {
        $response = $this->post('/profile', [
            'name' => 'Hacker',
            'password' => '',
            'password_confirmation' => '',
        ]);
        $response->assertRedirect('/login');
    }

    public function test_profile_update_fails_with_invalid_data()
    {
        $response = $this->actingAs($this->user)->post('/profile', [
            'name' => '',
            'email' => '', // ✅ included
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
    }
}
