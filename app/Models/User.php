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
   protected $appends = ['mason_category', 'aadhaar_doc_link', 'voter_doc_link'];

   public const TECHNICAL_ENGINEER = 1;
   public const MASON = 2;
   public const DEALER = 3;
   public const RSSD = 4;
   public const ADMIN = 5;
   public const SD = 6;
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
    public function getAadhaarDocLinkAttribute()
    {
        return (!empty($this->aadhaar_doc) && file_exists(public_path("aadhaar/".$this->aadhaar_doc))) ? asset("public/aadhaar/".$this->aadhaar_doc) : null ;
    }
    public function getVoterDocLinkAttribute()
    {
        return (!empty($this->voter_doc) && file_exists(public_path($this->voter_doc))) ? asset("public/".$this->voter_doc) : null ;
    }
   /*  public function getRoleNameAttribute(){
        return ($this->role == "" || null) ? "" : self::$roles[$this->role] ;
    } */
    
    // Branch Relation
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    // Created By
    public function by_created()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Linked TE
   public function te_linked()
   {
       return $this->belongsTo(User::class, 'parent', 'id');
   }

   // Mason Dealer
   public function mason_dealers()
   {
       return $this->hasMany(MasonDealer::class, 'mason_id', 'id');
   }

    

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
