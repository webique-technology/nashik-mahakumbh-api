<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleEnquiry extends Model
{
    protected $fillable = [
        'full_name',
        'mobile_number',
        'pickup_date',
        'return_date',
        'passengers',
        'vehicle_id'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}