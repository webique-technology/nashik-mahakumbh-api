<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItineraryTranslation extends Model
{
    protected $fillable = [
        'itinerary_id',
        'language_code',
        'itinerary_title',
        'description'
    ];
}
