<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;



class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'phone' => 'required|unique:users,phone',
        ]);
        $user = User::create($validatedData);
         $this->sendOTP($request->phone);
        return response()->json(['user' => $user, 'status'=>true]);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'phone' => 'required',
            'otp' => 'required',
            'role' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response()->json(['message' => 'This User does not exist, check your details'], 400);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response()->json(['user' => auth()->user(), 'access_token' => $accessToken]);
    }
    public function sendOTP($phone)
    {
        $user = User::where('phone', $phone)->first();
        $user->otp = rand(100000,999999);
        if($user)
        {
            $user->save();
            return response()->json(['status'=>true,'msg' => "OTP Sent Successfully"]);
        }else
        {
            return response()->json(['status'=>false, 'msg' => "send OTP failed"]);
        }
        
    }

}