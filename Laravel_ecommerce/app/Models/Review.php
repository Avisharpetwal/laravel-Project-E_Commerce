<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','user_id','rating','comment','video_path','approved'];

    public function product() { return $this->belongsTo(Product::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function images() { return $this->hasMany(ReviewImage::class); }
    public function media_files() {
    return $this->hasMany(ReviewMedia::class);
}

}
