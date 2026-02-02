<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiftingApprovalHistory extends Model
{
    use HasFactory;

    protected $table = "lifting_approval_history";
    protected $guarded = [];
}
