<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDisableHistory extends Model
{
    use HasFactory;

    protected $table = "user_disable_history";

    protected $fillable = [
        "user_id",
        "disable_date_time",
        "disable_reason",
        "point_deducted",
    ];

}
