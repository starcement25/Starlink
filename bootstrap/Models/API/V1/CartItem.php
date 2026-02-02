<?php

namespace App\Models\API\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
     protected $table = 'cart_item';

   

	    public function users()
		{
			return $this->belongsTo('App\Models\API\V1\User', 'user_id');

		}
		public function products()
		{
			return $this->belongsTo('App\Models\API\V1\Product', 'product_id');

		}

}
