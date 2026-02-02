<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MasonDealer extends Model
{
    use HasFactory;
    protected $table = "mason_dealers";
    protected $guarded = [] ;

    public function mason()
    {
        return $this->belongsTo(User::class);
    }
    public function dealer()
    {
        return $this->belongsTo(User::class);
    }
}
