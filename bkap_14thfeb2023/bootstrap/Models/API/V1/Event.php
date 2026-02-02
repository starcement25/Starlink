<?php

namespace App\Models\API\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
    	'id',
        'title',
        'hosted_by',
        'host_date',
       
    ];
	protected function serializeDate($date)
	{
  	  return $date->format('M d, Y');
	}
	 protected $hidden = [
        'updated_at',
    ];
    protected $casts = [
    'host_date'  => 'date:d M, Y',
   
];
}
