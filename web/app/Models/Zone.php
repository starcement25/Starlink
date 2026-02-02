<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zone extends Model
{
    use HasFactory;
    protected $guarded = [];

    public static $rules = [
        'name' => 'required|max:255',
        'status' => 'numeric'
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class, 'zone_id', 'id');
    }
}
