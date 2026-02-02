<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
      return view('admin.auth.login');
    }

    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
          ]);
    
          $credentials = $request->only('email', 'password');
          $credentials['status'] = 1;
          $adminUser = User::where('email', $request->email)->value('role');
         
          $adminRoleId = 5;

            if(in_array($adminUser, [$adminRoleId]))
            {
                if(\Auth::attempt($credentials, true)) {
                    return redirect()->route('products.index');
                }
            }
            return redirect()->route('admin.login')->withErrors(['email' => 'Invalid Username/password.']);
    }

    public function logout(Request $request)
    {
      Auth::logout(); // log the user out of our application
      $request->session()->invalidate();
      return redirect(route('admin.login')); // redirect the user to the login screen
    }
}
