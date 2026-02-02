<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\MasonDealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class BranchController extends Controller
{
    function getAllBranch(Request $request)
    {
        $branchs = Branch::orderBy('name','ASC')->get();
        return response()->json(['status'=> true, 'data' => $branchs, 'msg' => "Branch data fetch successfully"], 200);
    }
    function getBranchUser(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'branch_id' => 'required|exists:branch,id',
         );
        // validation 
        $validator  = Validator::make($input, $rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' => $res['msg']]);
        }
        $users = User::select('id','name')->where('branch_id',$request->branch_id)->whereIn('role',[3,4])->orderBy('name','ASC')->get();
        if($users->isEmpty())
        {
            return response()->json(['status'=> false, 'data' => $users, 'msg' => "No relative dealer there"], 200);
        }
        return response()->json(['status'=> true, 'data' => $users, 'msg' => "Branch dealer fetch successfully"], 200);
    }
}

