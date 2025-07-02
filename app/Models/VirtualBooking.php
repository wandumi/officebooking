<?php

namespace App\Models;

use App\Models\User;
use App\Models\VirtualOffice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualBooking extends Model
{
    /** @use HasFactory<\Database\Factories\VirtualBookingFactory> */
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function virtualOffice()
    {
        return $this->belongsTo(VirtualOffice::class);
    }

    protected $casts = [
        'selected_dates' => 'array',
    ];



}
