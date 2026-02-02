<?php

namespace App\Models\API\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
    	'id',
        'user_id',
        'sub_total',
        'charge',
        'total',
        'billing_address',
        'status',
    ];
	protected function serializeDate($date)
	{
  	  return $date->format('Y-m-d H:i:s');
	}
	public function orderItems()
    {
    	return $this->hasMany('App\Models\API\V1\OrderItem');
    }
}
