<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','slug','description','price','discount','sku','stock_qty','category_id','subcategory_id'
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title) . '-' . time();
            }
        });
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class,'subcategory_id');
    }

    public function reviews() 
    { 
        return $this->hasMany(\App\Models\Review::class);
    }

public function avgRating()
{
    return round($this->reviews()->where('approved', true)->avg('rating') ?? 0, 1);
}

}
