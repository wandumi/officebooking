<?php

namespace App\Models;

use App\Models\HelpDesk;
use App\Models\Boardroom;
use App\Models\VirtualOffice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'city'];  
    
    public function offices()
    {
        return $this->hasMany(Office::class);
    }

    public function boardrooms()
    {
        return $this->hasMany(Boardroom::class);
    }

    public function virtualOffices()
    {
        return $this->hasMany(VirtualOffice::class); 
    }

     public function helpDesk()
    {
        return $this->hasMany(HelpDesk::class);
    }
}
