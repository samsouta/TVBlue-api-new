<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremiumCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'amount',
        'currency',
        'is_used',
        'used_at'
    ];
}
