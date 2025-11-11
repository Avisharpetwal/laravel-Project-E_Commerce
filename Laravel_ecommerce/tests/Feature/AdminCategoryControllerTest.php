<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Create admin user
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    
    public function test_admin_can_view_category_index(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
        $response->assertViewHas('categories');
        $this->assertTrue($response->viewData('categories')->contains($parent));
    }

    
    public function test_admin_can_create_category(): void
    {
        $data = [
            'name' => 'New Category',
            'parent_id' => null,
        ];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.categories.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'New Category']);
    }

    
    public function test_admin_can_edit_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.categories.edit', $category->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.edit');
        $response->assertViewHas('category', $category);
    }

    
    public function test_admin_can_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.categories.update', $category->id), [
                             'name' => 'Updated Name',
                         ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Updated Name']);
    }

    
    public function test_admin_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->delete(route('admin.categories.destroy', $category->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    
    public function test_admin_can_get_subcategories_as_json(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.categories.subcategories', $parent->id));

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $child->id, 'parent_id' => $parent->id]);
    }
}
