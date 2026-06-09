<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppBanner extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    public function getImgAttribute()
    {
      // return asset('public/banner')."/".$this->attributes['img'];
        return "https://starlinkinfluencers.in/web/public/".$this->attributes['img'];
    }
   
}
