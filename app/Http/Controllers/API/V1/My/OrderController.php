<?php

namespace App\Http\Controllers\API\V1\My;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\API\V1\Order;
use App\Models\API\V1\OrderItem;
use Illuminate\Support\Facades\Validator;
use DB;
class OrderController extends Controller
{
    function orderList(Request $request)
    {
        $data = Order::where('user_id',$request->user()->id)->withCount('orderItems')->get();
       
        if(!$data->isEmpty())
        {
  
            $re_message = 'Order fetched successfully';
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$data]);
        }else
        {
            return response()->json(['status' => false, 'message' => 'Order not available', 'data'=>$data]);
        }
       
    }

    function orderPlace(Request $request)
    {
        $input = $request->all();
        $rules = array(
                    'sub_total' => 'required',
                    'charge' => 'required',
                    'total'  =>'required',
                    'billing_address' => 'required',
        			'products' =>'required',
                 );
        $messages = array(
                    );
        $re_message = "";

        $attributes = array(
            'sub_total' => 'sub total',
            'charge' => 'service charge',
            'billng_address' => 'billing address'
        );

        // validation 
        $validator  = Validator::make($input,$rules,$messages,$attributes);
        $res = validationFailer($validator);
        if ($res['status'] == false) {
            return response()->json(['status' => false,'message' =>$res['msg']]);
        }
       //creating order
        $validatedData = $validator->validated();
        $validatedData['user_id'] = $request->user()->id;
     	$validatedData['status'] = 1;
        $order = Order::create($validatedData);
    	
    	//creating items
        $productArr = json_decode($validatedData['products'], true);
    	$orderItems = array();
    	foreach($productArr as $value)
        {
           
        	$orderItems[] = array(
            						"product_id" => $value['product_id'],
            						"order_id" => $order->id,
            						"price" => $value['price'],
            						"quantity" => $value['quantity'],
            					 );
        	
        }
     	OrderItem::insert($orderItems);
        return response()->json(['status' => true, 'message' => "Order placed successfully", 'data'=>'']);
    }
	function orederDetails(Request $request,$order_id)
    {
		
        $order = Order::select('id','sub_total','charge','total','billing_address','created_at as order_date','status')->where('user_id',$request->user()->id)->where('id',$order_id)->withCount('orderItems')->first();
       
        if($order)
        {
  			$orderItems = DB::table('order_items')
            	->select('order_items.product_id','product_master.title','order_items.price','order_items.quantity')
                ->join('product_master', 'product_master.id', '=', 'order_items.product_id')
        		->where('order_items.order_id',$order_id)
                ->get();
            $re_message = 'Order detail fetched successfully';
        	$data = array(
						"order" => $order,
            			"order_items" => $orderItems
            		);
        
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$data]);
        }else
        {
            return response()->json(['status' => false, 'message' => 'Order not available',]);
        }
       	
    	
       
    }
	   
}
