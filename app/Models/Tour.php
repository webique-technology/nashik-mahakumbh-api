<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'status',
        'location',
        'highlights',
        'inclusions',
        'base_price',
        'offer_price',
        'taxes',
        'total_seats',
        'main_banner',
    ];

    protected $casts = [
        'highlights' => 'array',
        'inclusions' => 'array',
    ];

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class);
    }

    public function seoMeta()
    {
        return $this->hasOne(SeoMeta::class);
    }
}
