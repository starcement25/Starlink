<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\user_master;
use App\Model\Message;
use App\Model\msgto;
use Twilio\Rest\Client;
class MessageController extends Controller
{
    function sendMsg()
    {
        $msgs = Message::get();
        return view('superadmin/send-msg')->with('msgs',$msgs);
    }
    function sendingMsg(Request $req)
    {
        $u = new Message;
        $u->msg = $req->msg;
        $u->subject = $req->subject;
        $u->save();
        foreach($req->users as $user)
        {
            $m = new msgto;
            $m->user_id = $user;
            $m->msg_id = $u->id;
            $m->save();
             // use wordwrap() if lines are longer than 70 characters
            if( $req->has('email'))
            {
                $msg = wordwrap($u->msg,70);
                $user2 = user_master::where('id',$user)->first();
                $to  = $user2->email;
                mail($to,$u->subject,$msg);
            }
            if($req->has('mobile'))
            {
                $token = getenv("TWILIO_AUTH_TOKEN");
                $twilio_sid = getenv("TWILIO_SID");
                $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
                $country_code="+91";
                $user2 = user_master::where('id',$user)->first();
                $phone='+916264650470';//$user2->mobile;
				try{
					$twilio = new Client($twilio_sid, $token);
					$verify = $twilio->verify->v2->services($twilio_verify_sid)
					->verifications
					->create($phone,'dm');
				}
				catch(\Exception $e ){
					return response()->json($e->getMessage());
				}	
            }
        }
        
        $msgs = Message::get();
        return redirect()->route('superadmin.send-msg')->with(['success'=>'Sent Successfuly',$msgs]);
    }
    function deleteMsg(Request $req)
    {
        $msg = Message::where('id',$req->msg_id)->first();
         msgto::where('msg_id',$req->msg_id)->delete();
        $msg->delete();
        $msgs = Message::get();
        return redirect()->route('superadmin.send-msg')->with(['success'=>'Deleted Successfuly',$msgs]);
    }
}
