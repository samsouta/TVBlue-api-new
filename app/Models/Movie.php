<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'description',
        'posted_date',
        'duration',
        'view_count',
        'rating_total',
        'rating_count',
        'language',
        'released_year',
        'thumbnail_url',
        'video_url',
        'is_featured',
        'genre_id',
        'sub_genre_id',
        'video_type'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function subGenre()
    {
        return $this->belongsTo(SubGenre::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function watchlist()
    {
        return $this->hasMany(Watchlist::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'movie_tag');
    }

    public function actresses()
    {
        return $this->belongsToMany(Actress::class, 'actress_movie');
    }



    public function video_type()
    {
        return $this->belongsTo(VideoType::class, 'video_type_id');
    }

    // Define the method to get the like count for the movie
    public function getLikeCount()
    {
        return $this->likes()->count(); // This counts the number of likes for this movie
    }
}
