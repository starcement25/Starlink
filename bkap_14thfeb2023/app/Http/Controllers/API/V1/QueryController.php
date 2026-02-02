<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Query;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class QueryController extends Controller
{
    function getAllQuery(Request $request)
    {
        $queries = Query::orderBy('created_at','DESC')->get();
        return response()->json(['status'=> true, 'data' => $queries, 'msg' => "Branch data fetch successfully"], 200);
    }
    function addQuery(Request $request)
    {   
            $input = $request->all();
            $rules = array(
                        'email' => 'required|email',
                        'name' => 'required',
                        'message' => 'required',
                    );
            $validator  = Validator::make($input, $rules);
            $validRes = validateInput($validator);
            if ($validRes['status'] == false) {
                return response()->json(['status' => false, 'msg' => $validRes['msg']]);
            }
            $data = $validRes['validated_data'];
            Query::create($data);
            return response()->json(['status'=> true,'msg' => "Your query is register successfully we will back to you soon"], 200);
    }
}

