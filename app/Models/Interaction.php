<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    use HasFactory;

    protected $fillable = ['type',  'content', 'user_id', 'entity_id', 'entity_type'];


    public function subject()
    {
        return $this->morphTo();
    }


    public function interactable()
    {
        return $this->morphTo();
    }

    public function comments()
    {
        return $this->morphMany(Interaction::class, 'interactable');
    }

    public function likes()
    {
        return $this->comments()->where('interaction_type', 'like');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    // public function commentComments()
    // {
    //     return $this->comments()->where('interaction_type', 'comment');
    // }
}
