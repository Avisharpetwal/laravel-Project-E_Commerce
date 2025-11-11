<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_view_products_index()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.products.index'));

        $response->assertStatus(200)
                 ->assertViewIs('admin.products.index');
    }

    public function test_admin_can_view_create_product_form()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.products.create'));

        $response->assertStatus(200)
                 ->assertViewIs('admin.products.create');
    }

    public function test_admin_can_store_product_with_media_sizes_colors()
    {
        Storage::fake('public');
        $category = Category::factory()->create();

        $mediaImage = UploadedFile::fake()->image('product.jpg');
        $mediaVideo = UploadedFile::fake()->create('video.mp4', 1000, 'video/mp4');

        $data = [
            'title' => 'Full Product',
            'description' => 'Full Description',
            'price' => 150,
            'stock_qty' => 20,
            'category_id' => $category->id,
            'sizes' => 'S,M',
            'colors' => 'Red,Blue',
            'media' => [$mediaImage, $mediaVideo],
            'variant_stock' => 10,
            'variant_price' => 200,
        ];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.products.store'), $data);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::first();
        $this->assertEquals('Full Product', $product->title);

        // Images
        $this->assertCount(1, $product->images()->where('type', 'image')->get());
        $this->assertCount(1, $product->images()->where('type', 'video')->get());

        // Variants
        $this->assertCount(4, $product->variants); // S-Red, S-Blue, M-Red, M-Blue
    }

    public function test_admin_can_view_edit_form_with_product()
    {
        $product = Product::factory()->create();
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.products.edit', $product->id));

        $response->assertStatus(200)
                 ->assertViewIs('admin.products.create')
                 ->assertViewHas('product', $product);
    }


    public function test_admin_can_delete_product_with_media_and_variants()
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        $image = ProductImage::factory()->create([
            'product_id' => $product->id,
            'path' => 'products/test.jpg',
            'type' => 'image'
        ]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        Storage::disk('public')->put('products/test.jpg', 'dummy');

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin.products.destroy', $product->id));

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_images', ['id' => $image->id]);
        $this->assertDatabaseMissing('product_variants', ['id' => $variant->id]);
        Storage::disk('public')->assertMissing('products/test.jpg');
    }

    public function test_userdashboard_returns_products_with_filters()
    {
        $category = Category::factory()->create();
        $subCategory = Category::factory()->create(['parent_id' => $category->id]);

        Product::factory()->create([
            'title' => 'Apple',
            'price' => 100,
            'category_id' => $category->id
        ]);
        Product::factory()->create([
            'title' => 'Banana',
            'price' => 200,
            'subcategory_id' => $subCategory->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('user.dashboard', ['search' => 'Apple', 'min_price'=>50, 'max_price'=>150, 'category'=>$category->id]));

        $response->assertStatus(200)
                 ->assertViewIs('user.dashboard')
                 ->assertViewHas('products');
    }
}
