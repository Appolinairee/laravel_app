<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'user_id', 'subject_id', 'subject_type'];


    public function subject()
    {
        return $this->morphTo();
    }
    
}
