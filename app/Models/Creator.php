<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creator extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'logo',
        'description',
        'location',
        'delivery_options',
        'payment_options',
        'user_id'
    ];

    protected $hidden = [
        'user_id',
        'id'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user that owns the phone.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
