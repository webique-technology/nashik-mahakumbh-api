<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourEnquiry extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'number_of_travelers',
        'preferred_dates',
        'tour_id',
        'special_requirements',
        'vehicle_category_id'
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
    public function vehicleCategory()
    {
        return $this->belongsTo( VehicleCategory::class,'vehicle_category_id');
    }
}