<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewImageTest extends TestCase
{
    use RefreshDatabase;

    
    public function test_it_has_correct_fillable_fields()
    {
        $reviewImage = new ReviewImage();

        $this->assertEquals(
            ['review_id', 'path'],
            $reviewImage->getFillable()
        );
    }

    
    public function test_it_belongs_to_a_review()
    {
        $review = Review::factory()->create();
        $image = ReviewImage::factory()->create(['review_id' => $review->id]);

        $this->assertInstanceOf(Review::class, $image->review);
        $this->assertEquals($review->id, $image->review->id);
    }
}
