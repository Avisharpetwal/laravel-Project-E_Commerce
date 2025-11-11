<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Test user & product
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create();
    }

    
    public function test_authenticated_user_can_submit_review_with_rating_and_comment()
    {
        $reviewData = [
            'rating' => 5,
            'comment' => 'Great product!',
        ];

        $response = $this->actingAs($this->user)
                         ->post(route('reviews.store', $this->product->id), $reviewData);

        $response->assertStatus(302); 
        $response->assertSessionHas('success', 'Thanks — your review was posted!');

        $this->assertDatabaseHas('reviews', [
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'rating' => 5,
            'comment' => 'Great product!',
        ]);
    }

    
    public function test_authenticated_user_can_upload_video_for_review()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('review_video.mp4', 1000, 'video/mp4');

        $response = $this->actingAs($this->user)
                         ->postJson(route('reviews.uploadVideo', $this->product->id), [
                             'video' => $file
                         ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['path']);

        Storage::disk('public')->assertExists($response->json('path'));
    }

    
    public function test_review_submission_can_include_media_files()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('review_image.jpg');
        $video = UploadedFile::fake()->create('review_video.mp4', 1000, 'video/mp4');

        $reviewData = [
            'rating' => 4,
            'comment' => 'Nice product!',
            'media_files' => [$image, $video],
        ];

        $response = $this->actingAs($this->user)
                         ->post(route('reviews.store', $this->product->id), $reviewData);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Thanks — your review was posted!');

        $review = Review::first();
        $this->assertNotNull($review);

        // Check that image and video paths are saved
        Storage::disk('public')->assertExists($review->images()->first()->path);
        Storage::disk('public')->assertExists($review->media_files()->first()->path);
    }
}
