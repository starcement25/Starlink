<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\MasonDealer;
use App\Models\Reward;
use App\Models\RewardHistory;
use App\Models\Branch;
use App\Models\EmployeeBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\State;
use App\Models\Setting;
use App\Traits\HelperTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{
    use HelperTrait;
    function register(Request $request)
    {
        
        $input = $request->all();
        $rules = array(
                    'name' => 'required',
                    'phone' => 'required|min:10|max:10,|unique:users,phone',
                    'branch_id'  => 'required',
                    'te_id'      => 'required',
                    'dealer_ids'   => 'required',
                    'address1' => 'required',
                     'address2' => 'max:500',
                    'city' => 'required',
                    'district' => 'required',
                    'state' => 'required',
                     'country' => 'required',
                    'pincode' => 'required',        
                    'dob' => 'required',
                    'aadhaar_no' => "required|min:12|max:12",
                    'marital_status' => "required",
                    // 'phone_verified_at' => "required",
                 );
                 
        $messages = array(
                       "phone_verified_at.required" => "Please verify phone first",
                       "phone.min" => "invaild phone",
                       "phone.max" => "invailid phone",
                       "phone.unique" => "Mason is already registered."
                    );
        $attributes = array(
            'name' => 'mason name',
            'branch_id' => 'branch',
        	'dealer_ids' => 'dealer',
        	'aadhaar_no'=> 'aadhaar',
            'marital_status' => 'marital status',
            'phone' => 'mason phone',
            'phone_verified_at' => 'phone verified at'
        );

        // validation 
        $validator  = Validator::make($input,$rules,$messages,$attributes);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' =>$res['msg']]);
        }
        $validatedData = $validator->validated();
        unset($validatedData['te_id']) ;
        
        $validatedData['parent'] = $request->te_id;
        $validatedData['role'] = 2;
        $validatedData['spouse_name'] = $request->spouse_name;
        $validatedData['spouse_dob'] = $request->spouse_dob;
        // return $validatedData ;
        $settingName = Setting::where('setting_name','registration_point')->first();
        $validatedData['registration_point'] = $settingName->setting_value;
        try{
            DB::beginTransaction();
            $user = User::create($validatedData);
            if(isset($user->id)){
                if($request->file('aadhaar_doc')) {
                    $file = $request->file('aadhaar_doc');
                    $filename = "M".$user->id.".".$request->file('aadhaar_doc')->getClientOriginalExtension();
                    $location = base_path().'/public/aadhaar';
                    $file->move($location,$filename);
                    $user->aadhaar_doc = $filename;
                    $user->save();

                }
                // for add Registration Bonus points
                Reward::create([
                    'user_id'  => $user->id, 
                    'bag'         => 0, 
                    'description'         => 'Registration bonus points', 
                    'point'       =>  $this->getRegPoint(),
                    'is_verified' => 1 ,
                    'is_eligible_for_ledger' => RewardHistory::ELIGIBLE_FOR_LEDGER_YES ,
                    'is_bonus' => 1
                ]) ;
                $dealers =  json_decode($request->dealer_ids);
                $m_d = array();
                foreach($dealers as $dealer){
                    $m_d[] = array('mason_id' => $user->id, 'dealer_id' => $dealer);
                } 
                MasonDealer::insert($m_d);
                DB::commit(); 
                $this->updatePoint($user->id);
                return response()->json(['status'=> true, 'data' => $user, 'msg' => "Mason Registration sucessfully"]);
            }
           
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['status'=> false, 'msg' => $e->getMessage()]);
        }
        

    }

    public function teRegister(Request $request)
    {
        $input = $request->all();
        $rules = array(
                    'name' => 'required',
                    'phone' => 'required|min:10|max:10,|unique:users,phone',
                    'emp_code' => 'required|unique:users,phone',
                    'branch_ids'   => 'required',
                    'address1' => 'required',
                    'address2' => 'max:500',
                    'city' => 'nullable|max:255',
                    'district' => 'nullable|max:255',
                    'state' => 'nullable|max:255',
                    'pincode' => 'nullable|max:255',        
                 );
                 
        $messages = array(
                       "phone_verified_at.required" => "Please verify phone first",
                       "phone.min" => "invaild phone",
                       "phone.max" => "invailid phone",
                       "phone.unique" => "TE is already registered."
                    );
        $attributes = array(
            'name' => 'Te name',
            'branch_ids' => 'branch',
        	'phone' => 'te phone',
            
        );

        // validation 
        $validator  = Validator::make($input,$rules,$messages,$attributes);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'msg' =>$res['msg']]);
        }
        $validatedData = $validator->validated();
        unset($validatedData['branch_ids']);
        $validatedData['role'] = 1;
        $validatedData['designation'] = 'TE';
        try{
                DB::beginTransaction();
                    $user = User::create($validatedData);
                    $branches =  json_decode($request->branch_ids);
                    foreach ($branches as $key => $value) {
                        EmployeeBranch::create([
                            'user_id'=> $user->id,
                            'branch_id'=> $value,
                        ]);
                    }
                DB::commit();
                return response()->json(['status'=> true, 'data' => $user, 'msg' => "Te is Registered sucessfully."]);
        }
        catch(Exception $e){
            DB::rollBack();
            return response()->json(['status'=> false, 'msg' => $e->getMessage()]);
        }
        

        return $user;

    }
    
    // All Dealers For Testing Purpose.
    function getAllTestDealers(Request $request)
    {

        $users = User::whereIn('role',[3,4]);
        $data = $users->get();
               
        if($data->isEmpty()){
            return response()->json(['status' => false, 'msg' => 'No Dealer here', 'data' => $data]);  
        }
      
        return response()->json(['status' => true, 'msg' => 'Dealers get successfully', 'data' => $data]); 
      
    }
    public function getTestTe(Request $request)
    {
        $users = User::where('id', 210);
        $data = $users->get();
               
        if($data->isEmpty()){
            return response()->json(['status' => false, 'msg' => 'No Te here', 'data' => $data]);  
        }
      
        return response()->json(['status' => true, 'msg' => 'Te get successfully', 'data' => $data]); 
    }

    public function getTestTeBranch(Request $request)
    {
        $branch = Branch::whereIn('id', function($q){
            $q->select('branch_id')->from('employee_branches')->where('user_id', 210)->get() ;
        });
        $data = $branch->get();
               
        if($data->isEmpty()){
            return response()->json(['status' => false, 'msg' => 'No Branch here', 'data' => $data]);  
        }
      
        return response()->json(['status' => true, 'msg' => 'Branch get successfully', 'data' => $data]); 
    }

    public function getBranches(Request $request)
    {
        $data = Branch::all() ;
               
        if($data->isEmpty()){
            return response()->json(['status' => false, 'msg' => 'No Branch here', 'data' => $data]);  
        }
      
        return response()->json(['status' => true, 'msg' => 'Branch get successfully', 'data' => $data]); 
    }
    public function getStates(Request $request)
    {
        $data = State::select(['id', 'state_name'])->get() ;
               
        if($data->isEmpty()){
            return response()->json(['status' => false, 'msg' => 'No State here', 'data' => $data]);  
        }
      
        return response()->json(['status' => true, 'msg' => 'State retrieved successfully', 'data' => $data]); 
    }
}

