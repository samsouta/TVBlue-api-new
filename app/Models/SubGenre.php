<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubGenre extends Model
{
    protected $fillable = ['genre_id', 'name'];
    protected $hidden = ['created_at','updated_at'];


    
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}
