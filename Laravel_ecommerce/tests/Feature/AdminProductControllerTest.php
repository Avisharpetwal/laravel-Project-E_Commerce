<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminProductControllerTest extends TestCase
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

    
    public function test_admin_can_view_products_index()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.index');
    }

    
    public function test_admin_can_view_create_product_form()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.products.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.create');
    }

    
    public function test_admin_can_store_a_product()
    {
        $category = Category::factory()->create();

        $data = [
            'title' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'stock_qty' => 10,
            'category_id' => $category->id,
        ];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.products.store'), $data);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['title' => 'Test Product']);
    }

    
    public function test_admin_can_view_edit_product_form()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.products.edit', $product->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.create'); // Edit view is same as create
        $response->assertViewHas('product', $product);
    }

    
    public function test_admin_can_update_a_product()
    {
        $product = Product::factory()->create();

        $data = [
            'title' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 200,
            'stock_qty' => 5,
        ];

        $response = $this->actingAs($this->admin)
                         ->put(route('admin.products.update', $product->id), $data);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['title' => 'Updated Product']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_delete_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin.products.destroy', $product->id));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
