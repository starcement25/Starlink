<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Lifting;
use App\Models\MasonDealer;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class MasonController extends Controller
{
    
    function getMyMason(Request $request)
    {
        $users = User::where('role', 2);
        $users->select('users.id as mason_id','users.name as mason_name','users.aadhaar_no as mason_aadhaar_no','users.phone as mason_phone');        
        $data = $users->get();
        
        // if($request->user()->role == 1){
        //      $users = User::where('role', 2);
        //      $users->select('users.id as mason_id','users.name as mason_name','users.aadhaar_no as mason_aadhaar_no','users.phone as mason_phone');
        //      $data = $users->get();
        // }else
        // {
        //     $data = MasonDealer::join('users','users.id','=','mason_dealers.mason_id')
        //      ->where('dealer_id',$request->user()->id)
        //      ->select('users.id as mason_id','users.name as mason_name','users.aadhaar_no as mason_aadhaar_no','users.phone as mason_phone')
        //      ->get();
        // }
       
        if($data->isEmpty())
        return response()->json(['status' => false, 'msg' => 'No mason here', 'data' => $data]);  
        else
        return response()->json(['status' => true, 'msg' => 'Mason get fetch successfully', 'data' => $data]); 
    }
    function getDealersByMasonId(Request $request)
    {
        $input = $request->all();
        $rules = ['mason_id' => 'required'];
        $validator  = Validator::make($input,$rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' =>$res['msg']]);
        }
        $data = DB::table('mason_dealers')
        ->join('users as U','U.id','=','mason_dealers.dealer_id')
        ->where('mason_dealers.mason_id',$request->mason_id)
        ->select('U.id as dealer_id','U.name as dealer_name')
        ->get();
        if($data->isEmpty())
        return response()->json(['status' => false, 'msg' => 'No dealer here', 'data' => $data]);  
        else
        return response()->json(['status' => true, 'msg' => 'dealer get fetch successfully', 'data' => $data]); 

    }
    function getAllMasonRssd(Request $request)
    {
        $search = $request->query('search');
        $slen = strlen($search);
        $colum = 'phone';
        if( $slen == 12)
            $colum = 'aadhaar_no';
        $data = MasonDealer::where('dealer_id',$request->user()->id)->whereHas('mason',function($query) use ($colum,$search) {
            $query->where($colum,'like','%'.$search);
        })->get()->pluck('mason');
        if($data->isEmpty())
        return response()->json(['status' => false, 'msg' => 'No mason here', 'data' => $data]);  
        else
        return response()->json(['status' => true, 'msg' => 'Mason get fetch successfully', 'data' => $data]); 
    }
      
   function getAllDealers(Request $request)
    {
        $users = User::whereIn('role',[3,4]);      
        $data = $users->get();
               
        if($data->isEmpty())
        return response()->json(['status' => false, 'msg' => 'No Dealer here', 'data' => $data]);  
        else
        return response()->json(['status' => true, 'msg' => 'Dealers get successfully', 'data' => $data]); 
    }
}

