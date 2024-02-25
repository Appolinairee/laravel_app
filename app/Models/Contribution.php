<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contribution extends Model
{
    use SoftDeletes;

    protected $fillable = ['order_id', 'amount'];


    /**
     * Return correspondant order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }


}
