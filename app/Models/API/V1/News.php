<?php

namespace App\Models\API\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
	protected $table = 'news';
    protected $fillable = [
        'title',
        'content',
        'image',
    ];
   protected function serializeDate($date)
   {
  	  return $date->format('Y-m-d H:i:s');
   }
}
