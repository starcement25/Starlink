<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\HelperTrait;

class SettingController extends Controller
{
    use HelperTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('settings.view') ;
        $setting=Setting::where('is_visible',1)->get();
        return view('admin.settings.index')->with('settings',$setting);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('settings.create') ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('settings.create') ;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('settings.view') ;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('settings.edit') ;
        $setting=Setting::where('id',$id)->get()->first();
        //return $setting;
        return view('admin.settings.edit')->with('setting',$setting);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('settings.edit') ;
        $setting=Setting::find($id);
        if($setting->input_type == "image")
        {
            $validations = [
                'setting_value' => 'required|image|dimensions:width=414,height=896|max:10240',
            ];
        }
        else
        {
            $validations = [
                'setting_value' => 'required',
            ];
        }
        $request->validate($validations);
        if($setting->input_type == "image"){
            if(is_file(public_path($setting->setting_value)))
            {
                unlink(public_path($setting->setting_value)) ;
            }
            $data = $this->uploadFile($request->file('setting_value'), 'flash_banners') ;
            $setting->update(["setting_value" => $data['path'] ]);
        }
        else
        {
            $setting->update($request->all());
        }
        Flash::success("Setting Updated Successfully");
        return redirect()->route('settings.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('settings.delete') ;
        //
    }
}
