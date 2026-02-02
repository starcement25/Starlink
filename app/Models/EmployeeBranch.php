<?php

namespace App\Models;

use App\Models\User;
use App\Models\Branch;
use App\Models\EmployeeBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeBranch extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function branch_employee()
    {
        return $this->belongsToMany(User::class, 'employee_branches', 'branch_id', 'user_id');
    }

    public function get_te_branches()
    {
        return $this->belongsTo(EmployeeBranch::class,'branch_id');
    }





    
}
