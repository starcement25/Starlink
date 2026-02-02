<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\host_booked_date;

class BookingController extends Controller
{
     function booking()
     {
         $bookings = host_booked_date::get();
         return view('superadmin/booking')->with(['bookings' => $bookings]);
     }
     function cancelAction(Request $req)
     {
         $booking = host_booked_date::where('id',$req->id)->first();
         $booking->status = $req->value;
         if( $booking->save())
         {
            if($req->value == 0)
            {
                $msg = 'Cancel request for '.$booking->location->location_name.' is  Aproved your refund will be transfered to your account as per refund policy';
            }
            if($req->value == 3)
            {
                $msg = 'Sorry your Cancel request for '.$booking->location->location_name.' is Reject if you want know resion please contact to helpline ';
            }
           
            mail($booking->user->email,"Cancel Request Status for ".$booking->location->location_name,$msg);
            return response()->json(array('success'=> 1,'data' => ''));
         }else
         {
            return response()->json(array('success'=> 0,'data' => ''));
         }
        
         
        
     }
}
