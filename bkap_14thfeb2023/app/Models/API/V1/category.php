<?php

namespace App\Models\API\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class category extends Model
{
     protected $table = 'categories';

     public function subcategories()
	{
		return $this->hasMany('App\Model\sub_category_master', 'cate_id');

	}

    public function products()
	{
		return $this->hasMany('App\Models\API\V1\Product', 'cate_id');

	}
}
