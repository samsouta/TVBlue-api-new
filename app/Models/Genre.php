<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = ['name', 'description'];
    protected $hidden = ['created_at','updated_at'];

    
    public function movies()
    {
        return $this->hasMany(Movie::class);
    }

    public function subGenres()
    {
        return $this->hasMany(SubGenre::class);
    }
}
