<?php

namespace App\Http\Controllers\API\V1\Card;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\API\V1\Card;
use Illuminate\Support\Facades\Validator;
class CardController extends Controller
{
    function index(Request $request)
    {
        $data = Card::select('id','card_number','holder_name','exp_mnt','exp_y','active')->where('user_id',auth()->user()->id)->get();
       
        if(!$data->isEmpty())
        {
            $re_message = 'card fetched successfully';
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$data]);
        }else
        {
            return response()->json(['status' => false, 'message' => 'Card not available', 'data'=>$data]);
        }
       
    }
    function addCard(Request $request)
    {
        $input = $request->all();
        $rules = array(
                    'card_number' => 'required|min:16',
                    'holder_name' => 'required',
                    'exp_mnt'  =>'required',
                    'exp_y'   => 'required',
                 );
        $messages = array(
                        'card_number.min' => 'Invalid card number',
                    );
        $re_message = "";

        $attributes = array(
            'card_number' => 'card number',
            'holder_name' => 'holder name',
        	'exp_mnt' => 'expire month',
        	'exp_y'=> 'expire year',
        );

        // validation 
        $validator  = Validator::make($input,$rules,$messages,$attributes);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'message' =>$res['msg']]);
        }
        //card creation
        $validatedData = $validator->validated();
        $validatedData['user_id'] = auth()->user()->id;
        $user_card = Card::where('user_id', $validatedData['user_id'])->where('card_number',$validatedData['card_number'])->first();

        if($user_card)
        {
            return response()->json(['status' => false,'message' =>'Card already exist'], 400);
        }else
        {
            $c = Card::where('user_id', $validatedData['user_id'])->get()->count();
            if($c <= 0)
            {
                $validatedData['active'] = 1;
            }
            $res = Card::create($validatedData);
            $data = card::select('id','card_number','holder_name','exp_mnt','exp_y','active')->where('user_id',auth()->user()->id)->get();
            $re_message = 'card added successfully';
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$data]);
        }
       
    }
    function activeCard(Request $request)
    {
        $input = $request->all();
        $rules = array(
                    'card_number' => 'required|min:16',
                 );
        $messages = array(
                        'card_number.min' => 'Invalid card number',
                    );
        $re_message = "";

        $attributes = array(
            'card_number' => 'card number',
            
        );

        // validation 
        $validator  = Validator::make($input,$rules,$messages,$attributes);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'message' =>$res['msg']], 400);
        }
        //card creation
        $validatedData = $validator->validated();
        $validatedData['user_id'] = auth()->user()->id;
        $user_card = Card::where('user_id', $validatedData['user_id'])->where('card_number',$validatedData['card_number'])->first();

        if($user_card)
        {
            Card::where('user_id', $validatedData['user_id'])->update(['active'=>0]);
            $user_card->active = 1;
            $user_card->save();
            return response()->json(['status' => true,'message' =>'Card activeted successfully']);
        }else
        {
           
            $re_message = 'card not exist';
            return response()->json(['status' => false, 'message' => $re_message],400);
        }
       
    }
}
