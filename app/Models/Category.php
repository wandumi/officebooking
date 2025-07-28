<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'offers_level'];

    public function offices()
    {
        return $this->hasMany(Category::class);
    }

    protected $casts = [
        'offers_level' => 'boolean',
    ];

 
}
