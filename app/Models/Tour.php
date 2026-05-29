<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\VehicleCategory;

class Tour extends Model
{
    protected $fillable = [
        'title',
        'description',
        // 'category',
        'status',
        'location',
        'highlights',
        'inclusions',
        'base_price',
        'offer_price',
        'taxes',
        'total_seats',
        'main_banner',
        'vehicle_category_ids',
        'routes'
    ];

    protected $casts = [
        'highlights' => 'array',
        'inclusions' => 'array',
        'vehicle_category_ids' => 'array',
        'routes' => 'array',
    ];

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class);
    }

    public function seoMeta()
    {
        return $this->hasOne(SeoMeta::class);
    }
    public function vehicleCategories()
    {
        return VehicleCategory::whereIn(
            'id',
            $this->vehicle_category_ids ?? []
        )->get();
    }
}
