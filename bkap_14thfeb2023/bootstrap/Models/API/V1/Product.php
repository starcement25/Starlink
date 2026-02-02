<?php

namespace App\Models\API\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     protected $table = 'product_master';

     public function images()
          {
              return $this->hasMany('App\Models\API\V1\product_images', 'product_id');

          }

	    public function category()
		{
			return $this->belongsTo('App\Models\API\V1\category', 'cate_id');

		}
		public function sub_category()
		{
			return $this->belongsTo('App\Models\API\V1\sub_category_master', 'sub_cate_id');

		}

 public function carts()
          {
              return $this->hasMany('App\Models\API\V1\CartItem', 'product_id');

          }


}
