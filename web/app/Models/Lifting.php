<?php

namespace App\Models;

use App\Models\User;
use App\Models\Reward;
use App\Models\Product;
use App\Models\MasonLifting;
use App\Models\LiftingApprovalHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lifting extends Model
{
    use HasFactory;
    protected $table = "lifting";
    protected $guarded = [];

    // Lifting's Dealer User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    function liftingApprovalHistory()
    {
        return $this->hasMany(LiftingApprovalHistory::class, "lifting_id");
    }

    // Lifting's Mason User
    public function mason_user()
    {
        return $this->hasOne(MasonLifting::class, 'lifting_id', 'id');
    }


    // Lifting's  Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

   
    // Lifting's  Reward Point
    public function reward()
    {
       // return $this->hasOne(Reward::class, 'lifting_id', 'id');
       return $this->hasMany(Reward::class, 'lifting_id', 'id');
    }

    public static $rules = [
        'product_id' => 'required|integer',
        'user_id' => 'required|integer',
        'mason_id' => 'required|integer',
        'qty' => 'required|min:1',
        'lifting_date' => 'required|date',
        'img' => 'mimes:jpeg,jpg,png,gif|max:4096',
    ];

    public static function boot() {
        parent::boot();

        static::deleting(function($model) { // before delete() method call this
             $model->reward()->delete();
             $model->mason_user()->delete();
             $model->liftingApprovalHistory()->delete();
        });
    }
}
