<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPage extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    public static $rules = [
        "mobile"=>"required|digits:10|numeric",
        "address"=>"required",
    ];
}
