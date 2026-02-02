<?php

namespace App\Models;

use App\Models\Lifting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reward extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    public const UNVERIFIED = 0;
    public const VERIFIED = 1;
    public const REJECTED = 2;
    
    // Reward point belongs to Lifting.
    public function lifting()
    {
        return $this->belongsTo(Lifting::class, 'lifting_id');
    }
    public function mason()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
