<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'location',
        'email_verified_at',
        'balance',
        'affiliate_balance',
        'affiliate_code'
    ];

    /**
     * @var array
     */
    
    protected $appends = [
        'notification_count',
        'message_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the creator associated with the user.
     */
    public function creator()
    {
        return $this->hasOne(Creator::class);
    }


    /**
     * Check if a user has the 'admin' or 'moderator' role.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin' || $this->role === 'moderator';
    }



    /**
     * Get the number of unread notifications for the user.
     *
     * @return int
     */
    public function getNotificationCountAttribute()
    {
        return $this->notifications()->where('state', 0)->count();
    }



    /**
     * Get the number of unread messages for the user.
     *
     * @return int
     */
    public function getMessageCountAttribute()
    {
        return $this->messages()->where('status', 0)->count();
    }



    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }


    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
