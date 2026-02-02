<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

class TestController extends Controller
{
    use HelperTrait;
   
    public function sendNote(Request $request) {
  
        $token = "dOFseS5YR1aj5UGBHKvH1J:APA91bHiy06E79EJYJpOl4ave2bwmS_kLKmlr69ftMUv_sJWzn6nPMdbnXDmjWHLwGiHmq-4_cgk3D_sl1DEAC9t9uRuvNP7UoyiJFaOhwqAyUnu-RT3u_s" ;
        $title = "Hi" ;
        $message = "Lorem Ipsum" ;
        $data = ['data'=> 'My Data'];
        if(!empty($token)){
            return   $this->send_fcm_notification($token, $title, $message, $data)  ;
        }

       
   
    }
    
}
