<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    protected $fillable = [
        'title',
        'sub_title',
        'description',
        'first_button_name',
        'first_button_link',
        'second_button_name',
        'second_button_link',
        'status',
        'carousel_image'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];
}