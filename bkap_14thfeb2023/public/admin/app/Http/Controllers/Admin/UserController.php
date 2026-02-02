<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('id', 'DESC')->get();
       // return $users;
        return view('admin.user.index')->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $status = ['' => 'Select Status', '1'=> 'Married', '2'=> 'Un Married'];
        $roles  = ['' => 'Select Roles', '1'=> 'Technical Engineer', 
                    '2'=> 'Dealer', '3'=> 'Mason','4'=> 'RSSD', '5'=> 'Admin'];
       
        return view('admin.user.create')->with('statusOption', $status)
                ->with('roleOption', $roles)->with('status', 0)->with('roleSelected', 0) ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        // return $input ;
        $product = User::create($input);
        Flash::success('User saved successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $status = ['' => 'Select Status', '1'=> 'Married', '2'=> 'Un Married'];
        $roles  = ['' => 'Select Roles', '1'=> 'Technical Engineer', 
                    '2'=> 'Dealer', '3'=> 'Mason','4'=> 'RSSD', '5'=> 'Admin'];
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('users.index'));
        }

        return view('admin.user.edit')->with('user', $user)->with('statusOption', $status)
        ->with('roleOption', $roles)->with('status', array_search($user->status, array_keys($status)))->with('roleSelected', array_search($user->role, array_keys($roles))) ;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);
       
        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }
        $emailStatus = User::where('email', $request->email)->WhereNotIn('id', [$user->id])->get();

        if(count($emailStatus) > 0){
            return redirect()->back()->withErrors(['email' => 'This email is already taken.'])
                        ->withInput($request->input());
        }
        $input = !empty($request->password) ? $request->all() : $request->except('password') ;
        
        if(array_key_exists('password',  $input)){
            $input['password'] = Hash::make($input['password']);
        }
       // return $input;
        $user =  $user->update($input);;

        Flash::success('User updated successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('users.index'));
        }

        $user->delete();

        Flash::success('User deleted successfully.');
        return redirect(route('users.index'));
    }
}
