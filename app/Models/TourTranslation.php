<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourTranslation extends Model
{
    protected $fillable = [
        'tour_id',
        'language_code',
        'title',
        'description',
        'highlights',
        'inclusions',
        'routes'
    ];

    protected $casts = [
        'highlights' => 'array',
        'inclusions' => 'array',
        'routes' => 'array',
    ];
}
