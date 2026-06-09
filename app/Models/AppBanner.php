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
     public function zones()
    {
        return $this->belongsToMany(
            Zone::class,
            'banners_with_zones',
            'app_banners_id',
            'zones_id'
        );
    }
   
}
