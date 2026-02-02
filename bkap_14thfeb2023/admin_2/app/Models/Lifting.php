<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lifting extends Model
{
    use HasFactory;
    protected $table = "lifting";
    protected $guarded = [];

    // Lifting's User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Lifting's  Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public static $rules = [
        'product_id' => 'required|integer',
        'user_id' => 'required|integer',
        'qty' => 'required|min:1',
        'lifting_date' => 'required|date',
        'img' => 'mimes:jpeg,jpg,png,gif|max:4096',
    ];
}
