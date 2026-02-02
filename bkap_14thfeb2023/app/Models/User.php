<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use  App\Models\API\V1\Church;
use App\Models\Role;
use App\Models\Branch;
use App\Models\Lifting;
use App\Models\MasonDealer;
use App\Models\MasonCategory;
use Illuminate\Support\Facades\Hash;
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
   protected $guarded = [] ;
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
   


    public function getMasonCategoryAttribute()
    {
   //     return $this->points ;
        return !empty($this->points) ? MasonCategory::where('from_point','<=', $this->points)
        ->where('to_point','>=', $this->points)->first() : null ;
    }
   /*  public function getRoleNameAttribute(){
        return ($this->role == "" || null) ? "" : self::$roles[$this->role] ;
    } */
    
    // Branch Relation
    // public function branch()
    // {
    //     return $this->belongsTo(Branch::class, 'branch_id', 'id');
    // }

    

    // Role Relation
    // public function roles()
    // {
    //     return $this->belongsTo(Role::class, 'role', 'id');
    // }

    // Lifting Relation
    // public function lifting()
    // {
    //     return $this->hasMany(Lifting::class, 'user_id', 'id');
    // }
   
}
