<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'google_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    // User has many Likes
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // User has many Comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // User has many Ratings
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // User has many Watchlists
    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }

    // Optional: If you want to access movies that the user has liked (reverse relationship)
    public function likedMovies()
    {
        return $this->belongsToMany(Movie::class, 'likes');
    }

    // Optional: If you want to access movies that the user has rated (reverse relationship)
    public function ratedMovies()
    {
        return $this->belongsToMany(Movie::class, 'ratings');
    }
}
