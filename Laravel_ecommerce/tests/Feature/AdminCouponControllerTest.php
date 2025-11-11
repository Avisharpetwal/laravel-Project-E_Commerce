<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminCouponControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Admin user create
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_coupons_index()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.coupons.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.coupons.index');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_create_coupon_form()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.coupons.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.coupons.create');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_store_a_coupon()
    {
        $data = [
            'code' => 'TEST10',
            'discount_amount' => 10,
            'expiry_date' => now()->addDays(10)->format('Y-m-d'),
            'minimum_value' => 100,
        ];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.coupons.store'), $data);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', ['code' => 'TEST10']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_edit_coupon_form()
    {
        $coupon = Coupon::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.coupons.edit', $coupon->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.coupons.edit');
        $response->assertViewHas('coupon', $coupon);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_update_a_coupon()
    {
        $coupon = Coupon::factory()->create();

        $data = [
            'code' => 'UPDATED10',
            'discount_amount' => 15,
            'expiry_date' => now()->addDays(20)->format('Y-m-d'),
            'minimum_value' => 200,
        ];

        $response = $this->actingAs($this->admin)
                         ->put(route('admin.coupons.update', $coupon->id), $data);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', ['code' => 'UPDATED10']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_delete_a_coupon()
    {
        $coupon = Coupon::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin.coupons.destroy', $coupon->id));

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }
}
