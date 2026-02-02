<?php

namespace App\Http\Controllers\API\V1\My;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\API\V1\UserAddress;
use Illuminate\Support\Facades\Validator;
class AddressController extends Controller
{
     function index(Request $request)
    {
        $data = UserAddress::select('id','street','city','state','pincode','additional_info','selected')->where('user_id',auth()->user()->id)->get();
       
        if(!$data->isEmpty())
        {
            $re_message = 'Address fetched successfully';
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$data]);
        }else
        {
            return response()->json(['status' => false, 'message' => 'Address not available', 'data'=>$data]);
        }
       
    }
    function addAddress(Request $request)
    {
        $input = $request->all();
        $rules = array(
                    'street' => 'required',
                    'city' => 'required',
                    'state'  =>'required',
                    'pincode'=> 'required',
                 );
 
        $re_message = "";

        // validation 
        $validator  = Validator::make($input,$rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'message' =>$res['msg']]);
        }
        $validatedData = $validator->validated();
    	$validatedData['user_id'] = $request->user()->id;
    	$validatedData['additional_info'] = $request->additional_info;
    	if($request->has('default'))
        {
        	if($request->input('default') == 1)
            {
            	UserAddress::where('user_id',$request->user()->id)->update(['selected' => '0']);
        		$validatedData['selected'] = $request->input('default');
            }else
            {
            	$validatedData['selected'] = $request->input('default');
            }
        	
        }
        $res2 = UserAddress::create($validatedData);
        $re_message = 'Address added successfull';
        return response()->json(['status' => true, 'message' => $re_message, 'data'=> $res2]);
    }

	 function chageAddress(Request $request)
    {
        $input = $request->all();
        $rules = array(
                    'id' => 'required',
                 );
       

        // validation 
        $validator  = Validator::make($input,$rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'message' =>$res['msg']]);
        }
        //card creation
        $validatedData = $validator->validated();
        $validatedData['user_id'] = $request->user()->id;
        $user_address = UserAddress::where('user_id', $validatedData['user_id'])->where('id',$validatedData['id'])->first();

        if($user_address)
        {
            UserAddress::where('user_id', $validatedData['user_id'])->update(['selected'=>0]);
            UserAddress::where('user_id', $validatedData['user_id'])->where('id', $validatedData['id'])->update(['selected'=>1]);
            return response()->json(['status' => true,'message' =>'Address change successfully']);
        }else
        {
           
            $re_message = 'address not created';
            return response()->json(['status' => false, 'message' => $re_message]);
        }
       
    }
	   
}
