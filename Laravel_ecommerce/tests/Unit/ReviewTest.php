<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\ReviewMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    
    public function test_it_has_correct_fillable_fields()
    {
        $review = new Review();

        $this->assertEquals(
            ['product_id', 'user_id', 'rating', 'comment', 'video_path', 'approved'],
            $review->getFillable()
        );
    }

    
    public function test_it_belongs_to_a_product()
    {
        $product = Product::factory()->create();
        $review = Review::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Product::class, $review->product);
        $this->assertEquals($product->id, $review->product->id);
    }

    
    public function test_it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $review->user);
        $this->assertEquals($user->id, $review->user->id);
    }
    
    public function test_it_has_many_review_images()
    {
        $review = Review::factory()->create();
        $image = ReviewImage::factory()->create(['review_id' => $review->id]);

        $this->assertTrue($review->images->contains($image));
    }


}
