<?php

namespace App\Models;

use App\Models\User;
use App\Models\Catalogue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserCatalogueRedeemtion extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function catalogue()
    {
        return $this->belongsTo(Catalogue::class, 'catalogue_id', 'id');
    }
}
