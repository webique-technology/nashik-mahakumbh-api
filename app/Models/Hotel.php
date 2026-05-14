<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'title',
        'description',
        'rating',
        'category',
        'location',
        'features',
        'meals',
        'base_price',
        'offer_price',
        'images',
    ];

    protected $casts = [
        'features' => 'array',
        'images' => 'array',
    ];
}
