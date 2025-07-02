<?php

namespace App\Models;

use App\Models\Location;
use App\Models\VirtualBooking;
use Illuminate\Database\Eloquent\Model;

class VirtualOffice extends Model
{
    protected $fillable = [
        'location_id', 'virtualoffice_name', 'address', 'discount', 
        'phone_number', 'price', 'handling', 'duration', 'price_premium',
        'price_standard'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);  
    }

    public function bookings()
    {
        return $this->hasMany(VirtualBooking::class);
    }
}
