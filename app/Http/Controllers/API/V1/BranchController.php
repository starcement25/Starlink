<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\State;
use App\Models\MasonDealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\EmployeeBranch;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\GoogleTranslateService;
class BranchController extends Controller
{
    protected $googleTranslate;

    public function __construct(GoogleTranslateService $googleTranslate)
    {
        $this->googleTranslate = $googleTranslate;
    }

    function getAllBranch(Request $request)
    {
        $branchs = Branch::where('status',1)->orderBy('name','ASC')->get();
        return response()->json(['status'=> true, 'data' => $branchs, 'msg' => "Branch data fetch successfully"], 200);
    }

    function getTeBranch(Request $request)
    {
        // $input = $request->all();
        // $rules = array(
        //     'te_id' => 'required',
        //  );
        // validation 
        // $validator  = Validator::make($input, $rules);
        // $res = validationFailer($validator);
        // if ($res['status'] == false) {
        //     return response()->json(['status' => false,'msg' => $res['msg']]);
        // }
        
        // $users = EmployeeBranch::with('get_te_branches')->where([
        //     'user_id' => $userId,
        // ])->get();

        $userId = Auth::user()->id;
        if(!empty(Auth::user()->preferred_app_lang))
        {
            $page = 1;
            if($request->has("page") && $request->page != null)
            {
                $page = $request->page;
            }
            if($page < 1)
            {
                return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []], 404);
            }
            $limit = 6;
            $fetchDataFrom = $limit * ($page - 1);
            $users = Branch::whereIn('id', function($q)use($userId){
                $q->select('branch_id')->from('employee_branches')->where('user_id', $userId);
            })->where('status', 1)->skip($fetchDataFrom)->take($limit)->get();
            $targetLanguage = Auth::user()->preferred_app_lang;
            foreach($users as $key => $user)
            {
                $users[$key]->keyword = $user->name;
                // $users[$key]->name = $this->googleTranslate->translateText($user->name, $targetLanguage);
                $users[$key]->name = $user->name;
                $users[$key]->is_voter_require  = ($user->state->is_voter_require ?? 0) === State::VOTER_REQUIRE_YES;
            }
        }
        else
        {
            $users = Branch::whereIn('id', function($q)use($userId){
                $q->select('branch_id')->from('employee_branches')->where('user_id', $userId);
            })->where('status', 1)->get();
        }
        if($users->isEmpty())
        {
            return response()->json(['status'=> false, 'data' => $users, 'msg' => "TE Branches not Found."], 404);
        }
        return response()->json(['status'=> true, 'data' => $users, 'msg' => "TE Branches fetch successfully"], 200);
    }

    function getBranchUser(Request $request)
    {
        $targetLanguage = null;
        if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
        {
            $targetLanguage = $request->preferred_app_lang;
        }
        if(empty($targetLanguage) && \Auth::check() && !empty(\Auth::user()->preferred_app_lang))
        {
            $targetLanguage = \Auth::user()->preferred_app_lang;
        }
        $input = $request->all();
        $rules = array(
            'branch_id' => 'required|exists:branch,id',
         );
        // validation 
        $validator  = Validator::make($input, $rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' => $this->googleTranslate->translateText($res['msg'], $targetLanguage)]);
        }
        if(!empty($targetLanguage))
        {
            $page = 1;
            if($request->has("page") && $request->page != null)
            {
                $page = $request->page;
            }
            if($page < 1)
            {
                return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []], 404);
            }
            $limit = 1000;
            $fetchDataFrom = $limit * ($page - 1);
            $users = User::select('id','name')->where('branch_id',$request->branch_id)->whereIn('role',[3,4,6])->orderBy('name','ASC')->skip($fetchDataFrom)->take($limit)->get();
            foreach($users as $key=> $user)
            {
                $users[$key]->keyword = $user->name;
                // $users[$key]->name = $this->googleTranslate->translateText($user->name, $targetLanguage);
                $users[$key]->name = $user->name;
            }
        }
        else
        {
            $users = User::select('id','name')->where('branch_id',$request->branch_id)->whereIn('role',[3,4,6])->orderBy('name','ASC')->get();
        }
        if($users->isEmpty())
        {
            return response()->json(['status'=> false, 'data' => $users, 'msg' => $this->googleTranslate->translateText("No relative dealer there", $targetLanguage)], 404);
        }
        return response()->json(['status'=> true, 'data' => $users, 'msg' => $this->googleTranslate->translateText("Branch dealer fetch successfully", $targetLanguage)], 200);
    }
}

