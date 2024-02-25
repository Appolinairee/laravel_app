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
        'creator_id',
        'amount_paid',
        'payment_type',
        'payment_status'
    ];

    protected $casts = [
        'shipping_date' => 'datetime',
    ];



    public function user(){
        return $this->belongsTo(User::class);
    }


    public function order_items() {
        return $this->hasMany(OrderItem::class);
    }



    
    /**
     * Total currrent amount of order.
     *
     * @return float
     */
    public function calculateTotalAmount()
    {
        $orderItems = $this->order_items()->with('product')->get();

        return $orderItems->sum(function ($item) {
            return $item->product->current_price;
        });
    }


    /**
     * Return correspondant vendor for order.
     *
     */
    public function creator()
    {
        return $this->belongsTo(Creator::class);
    }


    /**
     * Return contributions for an order.
     */
    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }
}