<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
    ];

    protected $appends = ['type_label'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            '0' => 'Contribution',
            '1' => 'Livraison',
            '2' => 'Commande',
            default => 'Type inconnu',
        };
    }
}
