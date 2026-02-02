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
        "whatsapp_no" => 'required|digits:10|integer',
    ] ;

    public static $messages = [
        "fb_link.required" => "The facebook link field is required.",
        "twitter_link.required" => "The twitter link field is required.",
        "web_link.required" => "The web link field is required.",
        "whatsapp_no.required" => "WhatsApp No. is required.",
      
    ] ;
}
