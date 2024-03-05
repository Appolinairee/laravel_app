<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'title', 'state', 'user_id', 'link', 'notifiable_id', 'notifiable_type'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function notificationEntity()
    {
        return $this->morphTo();
    }
}