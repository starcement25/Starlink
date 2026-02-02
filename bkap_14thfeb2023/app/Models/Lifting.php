<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lifting extends Model
{
    use HasFactory;
    protected $table = 'lifting';
    protected $fillable = [
        'product_id',
        'qty',
        'lifting_date',
        'remark',
        'img', 
        'user_id',                
    ];
    function masonLifting()
    {
        return $this->hasMany('App\Models\MasonLifting');
    }
    function dealer()
    {
        return $this->belongsTo('App\Models\User');
    }


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
  
     
      // Lifting's  Reward Point
      public function reward()
      {
          return $this->hasOne(Reward::class, 'lifting_id', 'id');
      }
  
      public static $rules = [
          'product_id' => 'required|integer',
          'user_id' => 'required|integer',
          'qty' => 'required|min:1',
          'lifting_date' => 'required|date',
          'img' => 'mimes:jpeg,jpg,png,gif|max:4096',
      ];
  
       public static function boot() {
          parent::boot();
  
          static::deleting(function($user) { // before delete() method call this
               $user->reward()->delete();
          });
      }
  
   
}
