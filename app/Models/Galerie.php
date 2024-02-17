<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galerie extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'link',
        'type',
        'id_product',
    ];

    /**
     * Get the product of galleries.
    */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}