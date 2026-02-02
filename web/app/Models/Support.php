<?php

namespace App\Models;

use App\Models\UserCatalogueRedeemtion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Support extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    public function order()
    {
        return $this->belongsTo(UserCatalogueRedeemtion::class, 'order_id');
    }
}
