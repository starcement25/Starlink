<?php

namespace App\Http\Controllers\API\V1\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\V1\category;
use App\Models\API\V1\Product;
use App\Models\API\V1\CartItem;

use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
     function index(Request $request)
     {
        $datas = Product::orderBy('id','DESC')->get();  
        $re_message = 'All product list';
        return response()->json(['status' => true, 'message' => $re_message, 'data'=>$datas]);
     }


 function productDetail($pid,Request $request)
     {
        $datas = Product::where('id',$pid)->first();  
        $re_message = 'All product details';
        return response()->json(['status' => true, 'message' => $re_message, 'data'=>$datas]);
     }


  function cartItems(Request $request)
     {
        $datas = CartItem::orderBy('id','DESC')->get();  
        $re_message = 'All Cart Item list';
        return response()->json(['status' => true, 'message' => $re_message, 'data'=>$datas]);
     }

 function cartSave(Request $request)
     {
       // dd($request);
 
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




}
