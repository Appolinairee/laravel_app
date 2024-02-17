<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'caracteristics',
        'delivering',
        'old_price',
        'current_price',
        'creator_id'
    ];

    public function creator(){
        return $this->belongsTo(Creator::class);
    }

    public function galeries(){
        return $this->hasMany(Galerie::class);
    }
}
