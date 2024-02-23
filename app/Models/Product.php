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
        'creator_id',
        'deleted_at',
        'disponibility',
        'quantity'
    ];

    public function creator(){
        return $this->belongsTo(Creator::class);
    }

    public function medias(){
        return $this->hasMany(Media::class);
    }


    /**
     * The products that belong to the category.
    */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get similars products of this Product
    */
    public function similarProducts()
    {
        return Product::whereHas('categories', function ($query) {
            $query->whereIn('category_id', $this->categories->pluck('id'));
        })
        ->where('id', '<>', $this->id)
        ->orderByRaw('CASE WHEN title LIKE ? THEN 1 WHEN title LIKE ? THEN 2 ELSE 3 END', ["%{$this->title}%", "%{$this->title}%"])
        ->take(5)
        ->get();
    }


    /**
     * Get similars products of this Product
    */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
