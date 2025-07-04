<?php

namespace App\Models;

use App\Models\Location;
use App\Models\HotDeskBooking;
use Illuminate\Database\Eloquent\Model;

class HelpDesk extends Model
{
    protected $fillable = ['location_id','help_desk_name', 'price', 'duration','discount', 'desks'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function hotdeskbookings()
    {
        return $this->hasMany(HotDeskBooking::class);
    }
}
