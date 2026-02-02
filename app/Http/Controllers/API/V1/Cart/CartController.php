<?php

namespace App\Http\Controllers\API\V1\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\V1\category;
use App\Models\API\V1\Product;
use App\Models\API\V1\CartItem;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class CartController extends Controller
{
    

  function cartItems($uid, Request $request)
     {
       // $datas = CartItem::orderBy('id','DESC')->get();  
  
  $datas = DB::table('cart_item')
            ->join('product_master', 'product_master.id', '=', 'cart_item.product_id')
           ->join('users', 'users.id', '=', 'cart_item.user_id') 
            ->select('cart_item.*', 'product_master.title', 'product_master.price', 'product_master.img', 'product_master.discount', 'users.name', 'users.email')
            ->where("cart_item.user_id", $uid)->get();
        $re_message = 'All Cart Item list';
        return response()->json(['status' => true, 'message' => $re_message, 'data'=>$datas]);
     }

 function cartSave(Request $request)
     {
           //dd($request);
 
       $input = $request->all();
        $rules = array(
                    'product_id' => 'required',
                    'user_id' => 'required',
                    'quantity' => 'required',
                 );
        $re_message = "";

        // validation 
        $validator  = Validator::make($input,$rules);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'message' =>$res['msg']], 200);
        }
        $validData = $validator->validated();
        $data = CartItem::where('product_id',$validData['product_id'])->where('user_id',$validData['user_id'])->first(); 
        if($data)
        {
            $data->product_id = $validData['product_id'];
             $data->user_id = $validData['user_id'];
             $data->quantity = $validData['quantity'];        
        
            $data->save();
            $re_message = 'Cart Item updated Successfully';
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$data]);
        }else
        {
             $data = new CartItem;
             $data->product_id = $validData['product_id'];
             $data->user_id = $validData['user_id'];
             $data->quantity = $validData['quantity'];        
        
            $data->save();
            $re_message = 'Cart Item Added Successfully';
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$data]);
        }
      
 
 
     }

function cartDelete($id,Request $request)
     {
       if($id!="")
       {
       
        $datas =  CartItem::where('id', $id)->delete();
        $re_message = 'Cart Item Deleted';
        return response()->json(['status' => true, 'message' => $re_message, 'data'=>$datas]);
       }
      else
       {
      
            $re_message = 'id not found';
            return response()->json(['status' => false,'message' => $re_message], 200);
       
       }
       





     }




}
