<?php

namespace App\Models\API\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;
    protected $fillable = [
        'card_number',
        'holder_name',
        'user_id',
    	'exp_mnt',
    	'exp_y',
        'active'
    ];
}
