<?php

namespace App\Models\API\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferCode extends Model
{
    use HasFactory;
    protected $table = 'refer_code';
    protected $fillable = [
        'user_id',
        'code',
        
    ];
	
	
    protected $hidden = [
        'created_at',
        'updated_at',
    	'user_id',
    ];

}
