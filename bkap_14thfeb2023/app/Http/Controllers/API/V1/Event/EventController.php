<?php

namespace App\Http\Controllers\API\V1\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\API\V1\Event;
use Illuminate\Support\Facades\Validator;
class EventController extends Controller
{
    function index(Request $request)
    {
        $data = Event::select('id','title','hosted_by','host_date','created_at')->orderBy('host_date','DESC')->get();
       
        if(!$data->isEmpty())
        {
            $re_message = 'Event fetched successfully';
            return response()->json(['status' => true, 'message' => $re_message, 'data'=>$data]);
        }else
        {
            return response()->json(['status' => false, 'message' => 'Event not available', 'data'=>$data]);
        }
       
    }
   
    
}
