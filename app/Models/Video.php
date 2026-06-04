<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'video_link',
        'video_image',
        'title',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];
}