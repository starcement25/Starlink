<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\User;
use App\Models\State;
use App\Models\Branch;
use App\Models\Lifting;
use App\Models\MasonDealer;
use App\Models\MasonCategory;
use App\Models\EmployeeBranch;
use App\Models\DealerLinkageRequest;
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

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    public const ROLE_TE = 1;
    public const ROLE_MASON = 2;
    public const ROLE_DEALER = 3;
    public const ROLE_RSSD = 4;
    public const ROLE_ADMIN = 5;
    public const ROLE_SUB_DEALER = 6;
    public const ROLE_ASM = 19;
    public const ROLE_ACCOUNTS = 21;
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
    public static $adminRole = 5;
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static $rules = [
       'email' => 'required|email|unique:users',
    //    'emp_code' => 'max:255|string',
       'role' => 'required|integer',
       'allocated_branches' => 'required',
       'password' => 'required',
       'phone' => 'required|digits:10|unique:users',
       'name' => 'required|string|max:255',
    //    'dob' => 'date',
    //    'aadhaar_no' => 'required|digits:12',
    ];

    // public static $masonUpdateRules = [
    //     'name' => 'required|string|max:255',
    //     'branch_id' => 'required',
    //     'phone' => 'required|digits:10',
    //     'spouse_name' => 'required_if:marital_status,1|nullable|string',
    //     'spouse_dob' => 'required_if:marital_status,1|nullable|date',
    //     'marital_status' => 'required',
    //     'address' => 'string',
    //     'parent' => 'required|numeric',
    //     'dealers' => 'required',
    //     'dob' => 'date',
    //     'aadhaar_no' => 'required|digits:12',
    //  ];   

    public static $updateRules = [
       'email' => 'required|email|unique:users,email',
        'emp_code' => 'max:255|string',
       'role' => 'required|integer',
       'branch_id' => 'required',
       'phone' => 'required|unique:users',
       'name' => 'required|string|max:255',
       'dob' => 'date',
       'aadhaar_no' => 'required|digits:12',
    ];

    public function getUserStatusByValue(int $statusValue)
    {
        if($statusValue == self::STATUS_ACTIVE)
        {
            return "Active";
        }
        elseif($statusValue == self::STATUS_INACTIVE)
        {
            return "Disabled";
        }
        else
        {
            return "";
        }
    }
    public function getUserStatus()
    {
        if($this->status == self::STATUS_ACTIVE)
        {
            return "Active";
        }
        elseif($this->status == self::STATUS_INACTIVE)
        {
            return "Disabled";
        }
        else
        {
            return "";
        }
    }
    public function getUserStatusValue()
    {
        return $this->status;
    }
  
    public function getMasonCategoryAttribute()
    {
        //return $this->points ;
        return !is_null($this->points) ? MasonCategory::where('from_point','<=', $this->points)
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

    // Branch Relation For Employee
    // public function employee_branch()
    // {
    //     return $this->hasMany(EmployeeBranch::class, 'user_id', 'id');
    // }

    public function employee_branch()
    {
        return $this->belongsToMany(Branch::class, 'employee_branches', 'user_id', 'branch_id')->withPivot('id');
        ;
    }

    // Created By
    public function by_created()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    public function dealers()
    {
        return $this->hasOne(MasonDealer::class, 'dealer_id','id');
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

    
    public function states()
    {
        return $this->hasOne(State::class,'id','state');
    }

   // Linked TE
   public function te_linked()
   {
       return $this->belongsTo(User::class, 'parent', 'id');
   }
   public function dealerLinkingRequest()
   {
       return $this->belongsTo(DealerLinkageRequest::class, 'user_id', 'id');
   }
}
