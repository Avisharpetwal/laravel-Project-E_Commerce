<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
            'images.*' => 'nullable|image|max:5120', // 5MB each
            'video' => 'nullable|file|mimetypes:video/webm,video/mp4|max:51200', // up to 50MB
        ]);

        // Video  (WebRTC)
        $videoPath = null;
        if ($request->video_data) {
            $videoData = base64_decode(preg_replace('#^data:video/\w+;base64,#i', '', $request->video_data));
            $videoName = 'review_' . time() . '.webm';
            file_put_contents(storage_path('app/public/reviews/videos/'.$videoName), $videoData);
            $videoPath = 'reviews/videos/'.$videoName;
        }

        // Create review
        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'video_path' => $videoPath,
            'approved' => true,
        ]);

        // Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('reviews/images', 'public');
                $review->images()->create(['path' => $path]);
            }
        }

        // Video from uploaded file (optional)
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('reviews/videos', 'public');
            $review->video_path = $path;
            $review->save();
        }

        return back()->with('success', 'Thanks — your review was posted!');
    }

    public function uploadVideo(Request $request, Product $product)
    {
        $request->validate(['video' => 'required|file|mimetypes:video/webm,video/mp4|max:51200']);
        $path = $request->file('video')->store('reviews/videos', 'public');
        return response()->json(['path' => $path], 200);
    }
}
