<?php

namespace App\Models;

use App\Models\Amenity;
use App\Models\Booking;
use App\Models\Location;
use App\Models\BoardroomBooking;
use Illuminate\Database\Eloquent\Model;

class Boardroom extends Model
{
    protected $fillable = ['id','boardroom_name', 'location_id','seats', 'hourly_price', 'daily_price'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'boardroom_amenity');
    }

    public function boardroombookings()
    {
        return $this->hasMany(BoardroomBooking::class);
    }
}
