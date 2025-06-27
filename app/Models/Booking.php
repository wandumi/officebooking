<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];
    
    protected $table = 'office_bookings';

    protected $casts = [
        'selected_dates' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

}
