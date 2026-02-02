<?php

namespace App\Models\API\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;
	protected $table  = 'user_address';
    protected $fillable = [
        'street',
        'city',
        'state',
        'pincode',
        'additional_info',
        'user_id',
        'selected'
    ];
}
