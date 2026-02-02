<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    use HasFactory;
    protected $table    = "states";
    protected $guarded  = [];

    public const VOTER_REQUIRE_YES = 1;
    public const VOTER_REQUIRE_NO = 0;

}
