<?php

namespace App\Models;

use App\Models\User;
use App\Models\Lifting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasonLifting extends Model
{
    use HasFactory;
    protected $table = "mason_lifting" ;
    protected $guarded = [] ;

    
    public function lifting()
    {
        return $this->belongsTo(Lifting::class, 'lifting_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'mason_id');
    }
}
