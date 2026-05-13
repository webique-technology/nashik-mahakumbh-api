<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'name',
        'location',
        'status',
        'total_seats',
        'features',
        'category',
        'base_price',
        'car_image'
    ];

    protected $casts = [
        'features' => 'array',
    ];
}