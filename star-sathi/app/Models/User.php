<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use App\Models\Lifting;
use App\Models\MasonDealer;
use App\Models\MasonCategory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];
    protected $appends = ['mason_category'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public static $roles =   ['' => 'Select Roles', '1'=> 'Technical Engineer', 
                            '2'=> 'Mason', '3'=> 'Dealer','4'=> 'RSSD', '5'=> 'Admin'];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static $rules = [
       'email' => 'required|email|unique:users,email',
       'emp_code' => 'max:255|string',
       'role' => 'required|integer',
       'branch_id' => 'required',
       'password' => 'required',
       'phone' => 'required',
       'name' => 'required|string|max:255',
       'dob' => 'date',
       'aadhaar_no' => 'max:12',
    ];
    
    public static $masonCreateRules = [
      
       'branch_id' => 'required',
       'password' => 'required',
       'phone' => 'required',
       'marital_status' => 'required',
       'address' => 'string',
       'spouse_name' => 'required_if:marital_status,1|nullable|string',
       'spouse_dob' => 'required_if:marital_status,1|nullable|date',
       'parent' => 'required|numeric',
       'dealers' => 'required', 
       'dob' => 'date',
       'aadhaar_no' => 'max:12',
    ];

    public static $masonUpdateRules = [
        'name' => 'required|string|max:255',
        'branch_id' => 'required',
        'phone' => 'required',
        'spouse_name' => 'string',
        'spouse_dob' => 'date',
        'marital_status' => 'required',
        'address' => 'string',
        'parent' => 'required|numeric',
        'dealers' => 'required',
        'dob' => 'date',
        'aadhaar_no' => 'max:12',
     ];

    public static $dealerCreateRules = [
      
        'name' => 'required|max:255',
        'role' => 'required',
        'linked_dealer' => 'nullable',
        'branch_id' => 'required',
        'status' => 'required',
        'phone' => 'required',
        'whatsapp_no' => 'required'
    ];

    public static $dealerUpdateRules = [
      
        'name' => 'required|max:255',
        'role' => 'required',
        'linked_dealer' => 'nullable',
        'branch_id' => 'required',
        'status' => 'required',
        'phone' => 'required',
        'whatsapp_no' => 'required'
    ];

   

    public static $updateRules = [
       'email' => 'required|email',
       'emp_code' => 'max:255|string',
       'role' => 'required|integer',
       'branch_id' => 'required',
       'phone' => 'required',
       'name' => 'required|string|max:255',
       'dob' => 'date',
       'aadhaar_no' => 'max:12',
    ];
  
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
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    // Created By
    public function by_created()
    {
        return $this->belongsTo(User::class, 'parent');
    }

    // Linked Dealer
    public function dealer_linked()
    {
        return $this->belongsTo(User::class, 'linked_dealer', 'id');
    }

    // Mason Dealer
    public function mason_dealers()
    {
        return $this->hasMany(MasonDealer::class, 'mason_id', 'id');
    }

    

    // Role Relation
    public function roles()
    {
        return $this->belongsTo(Role::class, 'role', 'id');
    }

    // Lifting Relation
    public function lifting()
    {
        return $this->hasMany(Lifting::class, 'user_id', 'id');
    }


   
}
