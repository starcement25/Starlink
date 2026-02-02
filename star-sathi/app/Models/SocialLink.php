<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    
    public static $rules = [
        "fb_link" => "required|max:255",
        "twitter_link" => "required|max:255",
        "web_link" => "required|max:255",
    ] ;

    public static $messages = [
        "fb_link.required" => "The facebook link field is required.",
      
    ] ;
}
