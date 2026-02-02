<?php

namespace App\Models;

use App\Models\Lifting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reward extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    
    // Reward point belongs to Lifting.
    public function lifting()
    {
        return $this->belongsTo(Lifting::class, 'lifting_id');
    }
}
