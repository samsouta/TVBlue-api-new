<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_method',
        'transaction_id',
        'status',
        'amount',
        'currency',
        'is_lifetime',
        'starts_at',
        'expires_at'
    ];

    protected $casts = [
        'is_lifetime' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
