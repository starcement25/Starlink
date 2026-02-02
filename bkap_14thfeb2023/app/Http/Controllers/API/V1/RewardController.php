<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class RewardController extends Controller
{
   
    function getRewards(Request $request)
    {   
            $rewards = DB::table('lifting')
            ->join('users as U','U.id','=','lifting.user_id')
            ->join('rewards as R','R.lifting_id','=','lifting.id');
            if($request->user()->role == 2) {
               $rewards = $rewards->where('R.user_id',$request->user()->id);         
            }
            $rewards =  $rewards->select('R.id','R.point','R.bag','R.show_point','R.is_verified','U.name as mason_name','U.aadhaar_no as mason_aadhaar_no','U.phone as mason_phone','R.created_at as reward_date','R.description')
            ->where('R.is_verified',1)
            ->orderByDesc('R.id')                   
            ->get();
            if($rewards->isEmpty()) {
                return response()->json(['status'=> false,'msg' => "no reward awarded",'get_reward' => true, 'data' => []], 200);
            }
            return response()->json(['status'=> true,'msg' => "rewards get successfully", 'get_reward' => true, 'data' => $rewards], 200);
    }

  function getRewardsByMason(Request $request)
    {   
        $input = $request->all();
        $rules = array(
                    'user_id' => 'required'
                );
        $validator  = Validator::make($input, $rules);
        $validRes = validateInput($validator);
        if ($validRes['status'] == false) {
            return response()->json(['status' => false, 'msg' => $validRes['msg']]);
        } 
        $role = $request->user()->role; 
        $id   = $request->user_id;
            $rewards = DB::table('lifting')
            ->join('users as U','U.id','=','lifting.user_id')
            ->join('rewards as R','R.lifting_id','=','lifting.id');
            $rewards = $rewards->where('R.user_id',$request->user_id);       
            $rewards =  $rewards->select('R.id','R.point','R.bag','R.show_point','R.is_verified','U.name as mason_name','U.aadhaar_no as mason_aadhaar_no','U.phone as mason_phone','R.created_at as reward_date','R.description')
            ->where('R.is_verified',1)
            ->orderByDesc('R.id')                   
            ->get();
            if($rewards->isEmpty()) {
                return response()->json(['status'=> false,'msg' => "no reward awarded", 'get_reward' => true, 'data' => []], 200);
            }
            return response()->json(['status'=> true,'msg' => "rewards get successfully", 'get_reward' => true, 'data' => $rewards], 200);
    }


}

