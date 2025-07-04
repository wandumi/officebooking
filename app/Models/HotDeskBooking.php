<?php

namespace App\Models;

use App\Models\User;
use App\Models\HelpDesk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotDeskBooking extends Model
{
    /** @use HasFactory<\Database\Factories\HotDeskBookingFactory> */
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function helpdesk()
    {
        return $this->belongsTo(HelpDesk::class);
    }

    protected $casts = [
        'selected_dates' => 'array',
        'time_slots' => 'array',
    ];

}
