<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\user_master;


class loginController extends Controller
{
	
    public function login()
	{
		return view('login');
	}

	public function signUp()
	{
		return view('sign-up');
	}
	
	public function submitLogin(Request $request)
	{
		$user = user_master::where('email',$request->username)->where('pass',$request->password)->where('active',1);
		if($user->count() > 0)
		{  
	        $userdata = $user->first();
			session()->put('userdata',$userdata);
			session()->put('role',$userdata->role);
			if ($userdata->role == '1') {
				return redirect()->route('admin.dashboard');
			}
			
		}
		
		return back()->with('error','Login failed! due to invalid Username Or Password Or Blocked User.');
	}
	

	public function submitRegister(Request $request)
	{

		$user = user_master::where('email',$request->email)->where('active',1);
		if($user->count() < 1)
		{  

			$users =new user_master;
			$users->user_fn = $request->username;			
			$users->email = $request->email;			
			$users->pass = $request->password;			
    	    $users->save();

			//save the session variables  
			$users = user_master::where('email',$request->username)->where('pass',$request->password)->where('active',1);
			if($user->count() > 0)
			{  
				
				$userdata = $user->first();
				session()->put('userdata',$userdata);
				session()->put('role',$userdata->role);
				if ($userdata->role == '1') {
					//dd("Line1111");
					return redirect()->route('admin.dashboard');
				}
				
			}
			
		}

		//return back()->with('error','Sign up failed! due to email already exist.');
	}
	


	public function verifyEmail(Request $request)
	{
		//dd($request);
		// the message.
		$otp=13455;
		$msg = "Verify your mail by entering the code : \n  Code -  $otp";

		// use wordwrap() if lines are longer than 70 characters
		$msg = wordwrap($msg,70);

		// send email
		mail("rewatiramansahu@gmail.com","Email Verify",$msg);
	}
	
	public function logout(Request $request)
	{
		$request->session()->flush();
		return redirect('login');
	}
	

	
	
}
