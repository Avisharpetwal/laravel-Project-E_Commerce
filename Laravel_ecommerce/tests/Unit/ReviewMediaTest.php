<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Review;
use App\Models\ReviewMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewMediaTest extends TestCase
{
    use RefreshDatabase;

    
    public function test_it_has_correct_fillable_fields()
    {
        $reviewMedia = new ReviewMedia();

        $this->assertEquals(
            ['review_id', 'path', 'type'],
            $reviewMedia->getFillable()
        );
    }

    
    public function test_it_belongs_to_a_review()
    {
        $review = Review::factory()->create();
        $media = ReviewMedia::factory()->create(['review_id' => $review->id]);

        $this->assertInstanceOf(Review::class, $media->review);
        $this->assertEquals($review->id, $media->review->id);
    }
}
