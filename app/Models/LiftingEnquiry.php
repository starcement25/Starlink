<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiftingEnquiry extends Model
{
    use HasFactory;
    protected $table = 'lifting_enquiries';
    protected $fillable = [
        'enquiry_by',
        'enquiry_to',
        'product_id',
        'quantity',
        'date_of_lifting', 
        'lifting_query'          
    ];

    public function product()
    {
        return $this->hasOne(Product::class, "product_id", "id");
    }

    public function enquiryBy()
    {
        return $this->hasOne(User::class, "enquiry_by", "id");
    }

    public function enquiryTo()
    {
        return $this->hasOne(User::class, "enquiry_to", "id");
    }
  
   
}
