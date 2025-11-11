<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_with_mass_assignment()
    {
        $user = User::factory()->create([
            'name' => 'Avi',
            'email' => 'avi@example.com',
            'password' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'avi@example.com',
            'name' => 'Avi',
        ]);

        $this->assertTrue(password_verify('password123', $user->password));
    }

    public function test_user_orders_relation()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->orders->contains($order));
        $this->assertEquals(1, $user->orders()->count());
    }

    public function test_user_reviews_relation()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->reviews->contains($review));
        $this->assertEquals(1, $user->reviews()->count());
    }

    public function test_user_hidden_attributes()
    {
        $user = User::factory()->create();
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    public function test_user_casted_attributes()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
    }
}
