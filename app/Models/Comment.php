<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    protected $fillable = [
        'user_id',
        'movie_id',
        'comment_text',
        'created_at',
    ];
    protected $hidden = ['updated_at'];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Movie
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
