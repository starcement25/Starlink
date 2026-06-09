<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Zone;

class Banner extends Model
{
    use HasFactory;
    protected $table = "app_banners" ;
    protected $guarded = [] ;

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
