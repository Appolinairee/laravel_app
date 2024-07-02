<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'balance', 'wallet_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function walletTransactions () {
        return $this->hasMany(WalletTransaction::class);
    }


    // wallet_type:  'user' | 'creator'
    // default('user')

}
