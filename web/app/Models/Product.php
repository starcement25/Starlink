<?php

namespace App\Models;

use App\Models\Lifting;
use App\Models\RewardPoint;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string||max:255',
        'description' => 'required',
        'bag' => 'required',
        'point' => 'required',
    ];

    public function lifting()
    {
        return $this->hasMany(Lifting::class, 'product_id', 'id');
    }

    public function reward_point()
    {
        return $this->hasOne(RewardPoint::class, 'product_id', 'id');
    }

    
}
