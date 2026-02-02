<?php

namespace App\Models;
use App\Models\Lifting;
use App\Models\RewardPoint;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description'];

    public function lifting()
    {
        return $this->hasMany(Lifting::class, 'product_id', 'id');
    }

    public function reward_point()
    {
        return $this->hasOne(RewardPoint::class, 'product_id', 'id');
    }
}
