<?php

namespace App\Models;

use App\Models\Lifting;
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
        'description' => 'required'
    ];

    public function lifting()
    {
        return $this->hasMany(Lifting::class, 'product_id', 'id');
    }

    
}
