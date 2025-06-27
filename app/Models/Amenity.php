<?php

namespace App\Models;

use App\Models\Office;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Amenity extends Model
{
    protected $fillable = ['amenity_name', 'price', 'description'];

    public function offices()
    {
        return $this->belongsToMany(Office::class);
    }

    public function boardrooms()
    {
        return $this->belongsToMany(Boardroom::class, 'boardroom_amenity');
    }

}
