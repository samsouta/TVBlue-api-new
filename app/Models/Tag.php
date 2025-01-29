<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    protected $hidden = ['created_at', 'updated_at'];

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_tag');
    }
}
