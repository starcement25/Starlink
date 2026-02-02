<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    use HasFactory;
    protected $table    = "states";
    protected $guarded  = [];

    public function branches()
    {
        return $this->hasMany(Branch::class, 'branch_id', 'id');
    }
}
