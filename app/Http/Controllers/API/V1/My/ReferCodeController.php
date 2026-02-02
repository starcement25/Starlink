<?php

namespace App\Http\Controllers\API\V1\My;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\API\V1\ReferCode;
use Illuminate\Support\Facades\Validator;
class ReferCodeController extends Controller
{
     function index(Request $request)
    {
        $data = ReferCode::select('id','code')->where('user_id',$request->user()->id)->first();
       
        if($data)
        {
            $re_message = 'Code fetched successfully';
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$data]);
        }else
        {
            $mycode = new ReferCode;
        	$mycode->user_id = $request->user()->id;
        	$mycode->code =$this->generateRandomString(6);
        	$mycode->save();
            $re_message = 'Code fetched successfully';
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$mycode]);
        }
       
    }
    public  function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
	   
}
