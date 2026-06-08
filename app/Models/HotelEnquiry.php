<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelEnquiry extends Model
{
    protected $fillable = [
        'hotel_id',
        'full_name',
        'email',
        'mobile_number',
        'room_type',
        'check_in_date',
        'check_out_date',
        'adults',
        'children'
    ];


    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}