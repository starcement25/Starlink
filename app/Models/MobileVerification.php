<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileVerification extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    protected $table = 'mobile_verification';


 public function getImagePathAttribute()
    {
        return asset('public/support')."/".$this->attributes['image_path'];
    }

}
