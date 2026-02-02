<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Lifting;
use App\Models\MasonLifting;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class LiftingController extends Controller
{
    
    function addLifting(Request $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->all();
            $rules = array(
                        'product_id' => 'required',
                        'mason_ids' => 'required',
                        'qty' => 'required',
                        'lifting_date' => 'required',
                    );
            if($request->hasFile('img'))
            {
                $rules = array_merge($rules,['img' => 'mimes:jpeg,jpg,png|required']);
            }

            // if($request->user()->role == 1){
            //     $rules = array_merge($rules,['dealer_rssd_id' => 'required']);
            //     $user_id = $request->dealer_rssd_id;
            // }else{
            //     $user_id = $request->user()->id;
            // }
            $user_id = $request->user()->id;
            $validator  = Validator::make($input, $rules);
            $validRes = validateInput($validator);
            if ($validRes['status'] == false) {
                return response()->json(['status' => false, 'msg' => $validRes['msg']]);
            }
            $data =  $validRes['validated_data'];
            $data['user_id'] = $user_id;
            $data['remark'] = $request->remark;
            $lifting = Lifting::create($data);
            $masons =  json_decode($request->mason_ids);
            $m_l = array();
            foreach($masons as $mason){
                $m_l[] = array('lifting_id' => $lifting->id, 'mason_id' => $mason);
            }
            MasonLifting::insert($m_l);
            if($request->file('img')) {
                $file = $request->file('img');
                $filename = "L".$lifting->id.".".$request->file('img')->getClientOriginalExtension();
                $location = base_path().'/public/lifting';
                $file->move($location,$filename);
                $lifting->img = asset('/public/lifting').'/'.$filename;
                $lifting->save();
            }
            DB::commit();
            return response()->json(['status' => true, 'msg' => 'lifting add successfully']);   
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        } 
    }
    function liftingHistory(Request $request) {
        $role = $request->user()->role; 
        $id   = $request->user()->id;
        $editable = false;
        if($role == 1)  {
            $editable = true;
            $liftings = $this->liftingHistoryCoreQuery();
            $liftings = $this->liftingHistoryCoreSelect($liftings);
            $liftings = $liftings->get();
        }else {
            $liftings = $this->liftingHistoryCoreQuery();
            $liftings = $liftings->where('L.user_id',$request->user()->id);
            $liftings = $this->liftingHistoryCoreSelect($liftings);
            $liftings = $liftings->get();
        }
        if($liftings->isEmpty())  
        return response()->json(['status'=> false, 'msg' => "No Data", 'data' => []]);
        else
        return response()->json(['status'=> true, 'msg' => "History get successfully ", 'data' => $liftings, 'editable' => $editable]);
        
    }

    function liftingHistoryCoreQuery() {
        $liftings = DB::table('mason_lifting as ML')
        ->LeftJoin('lifting as L','L.id','=','ML.lifting_id')
        ->Join('products as P','P.id','=','L.product_id')
        ->LeftJoin('users as U','U.id','L.user_id')
        ->LeftJoin('users as U2','U2.id','ML.mason_id');

        return $liftings;
    }
    function liftingHistoryCoreSelect($query){
        $query->select('L.id as lifting_id','U2.name as mason_name','U2.phone as mason_phone','U2.aadhaar_no as mason_aadhaar_no','U2.id as mason_id', 'U.id as dealer_id', 'U.name as dealer_name','P.id as product_id','P.name as product_name','L.qty','L.lifting_date','L.remark','L.id as points','U.name as mason_category');
        return $query;
    } 
    function updateLifting(Request $request)
    {
            $input = $request->all();
            $rules = array(
                        'id' => 'required',
                        'product_id' => 'required',
                        'qty' => 'required',
                        'lifting_date' => 'required',
                    );
            if($request->hasFile('img'))
            {
                $rules = array_merge($rules,['img' => 'mimes:jpeg,jpg,png|required']);
            }
            $validator  = Validator::make($input, $rules);
            $validRes = validateInput($validator);
            if ($validRes['status'] == false) {
                return response()->json(['status' => false, 'msg' => $validRes['msg']]);
            }
            $data =  $validRes['validated_data'];
            if($request->remark){
                $data['remark'] = $request->remark;
            }
            Lifting::where('id', $request->id)->update($data);
            return response()->json(['status' => true, 'msg' => "lifting updated successfully"]);
    }     
}

