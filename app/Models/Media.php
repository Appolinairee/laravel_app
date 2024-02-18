<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $table = 'medias';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'link',
        'type',
        'product_id',
    ];

    /**
     * Get the product of galleries.
    */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
