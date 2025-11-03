<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    // Mass assignable fields
    protected $fillable = [
        'code',
        'discount_amount',
        'discount_type',
        'expiry_date',
        'minimum_value',
    ];

     protected $casts = [
        'expiry_date' => 'date', 
    ];

}
