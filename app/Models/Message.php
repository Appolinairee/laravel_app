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
        'email_verified_at',
        'status'
    ];
    
    public function receiver()
    {
        if($this->receiver_type == "vendor"){
            return $this->belongsTo(Creator::class, 'receiver_id');
        }

        if($this->receiver_type == "user"){
            return $this->belongsTo(User::class, 'receiver_id');
        }
    }

    

    
    public function sender()
    {
        return $this->belongsTo(User::class);
    }

}