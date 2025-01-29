<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actress extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image_url',
        'age',
        'nationality',
        'birth_date',
        'is_popular'
        
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'birth_date' => 'date',
        'is_popular' => 'boolean'
    ];

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'actress_movie');
    }
}
