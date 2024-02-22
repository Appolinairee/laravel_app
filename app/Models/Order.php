<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'shipping_address',
        'shipping_price',
        'shipping_preview',
        'shipping_service',
        'shipping_date',
    ];

    protected $casts = [
        'shipping_date' => 'datetime',
    ];



    public function user(){
        return $this->belongsTo(User::class);
    }
}
