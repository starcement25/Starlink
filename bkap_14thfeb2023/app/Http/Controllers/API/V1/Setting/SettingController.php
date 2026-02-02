<?php

namespace App\Http\Controllers\API\V1\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\V1\Setting;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    function index(Request $request)
     {
        $settings = Setting::select('id','key','value')->get();  
        $re_message = 'All Church list';
        return response()->json(['status' => true, 'message' => $re_message, 'data'=>$settings]);
     }
     function changeSetting(Request $request)
     {
        $input = $request->all();
        $rules = array(
                    'id' => 'required',
                    'value' => 'required',
                 );
        $re_message = "";

        // validation 
        $validator  = Validator::make($input,$rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'message' =>$res['msg']], 400);
        }
        $validData = $validator->validated();
        $setting = Setting::select('id','key','value')->where('id',$validData['id'])->first(); 
        if($setting)
        {
            $setting->value = $validData['value'];
            $setting->save();
            $re_message = 'Setting Changed Successfully';
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$setting]);
        }else
        {
            $re_message = 'Invalid Setting';
            return response()->json(['status' => false, 'message' => $re_message, 'data'=>$setting]); 
        }
      
        
     }
     
     function addSetting(Request $request)
     {
        $input = $request->all();
        $rules = array(
                    'key' => 'required',
                    'value' => 'required',
                 );
       

        // validation 
        $validator  = Validator::make($input,$rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'message' =>$res['msg']], 400);
        }
        //setting creation
        $validatedData = $validator->validated();
        $setting = Setting::where('key', $validatedData['key'])->first();

        if($setting)
        {
            return response()->json(['status' => false,'message' =>'Setting already exist'], 400);
        }else
        {
            $res = Setting::create($validatedData);
            $data = Setting::select('id','key','value')->where('key',$validatedData['key'])->get();
            $re_message = 'Setting added successfully';
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$data]);
        }
     }
}
