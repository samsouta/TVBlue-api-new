<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoType extends Model
{
    public function movies()
    {
        return $this->hasMany(Movie::class, 'video_type_id');
    }
}
