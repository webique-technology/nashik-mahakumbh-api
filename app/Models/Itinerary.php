<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ItineraryTranslation;

class Itinerary extends Model
{
      protected $fillable = [
        'itinerary_title',
        'description',
        'image',
        'tour_id',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
    public function translations()
    {
        return $this->hasMany(ItineraryTranslation::class);
    }
}
