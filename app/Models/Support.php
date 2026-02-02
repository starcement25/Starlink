<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\UserCatalogueRedeemtion;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;
    protected $guarded = [] ;


    public function getImagePathAttribute()
    {
        return asset('public/support')."/".$this->attributes['image_path'];
    }
    public function order()
    {
        return $this->belongsTo(UserCatalogueRedeemtion::class, 'order_id');
    }

}
