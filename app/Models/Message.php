<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'type',
        'receiver_type',
        'sender_id',
        'receiver_id',
        'email_verified_at'
    ];
    
    public function receiver()
    {
        return $this->morphTo(null, 'receiver_type', 'receiver_id');
    }

}