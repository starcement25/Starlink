<?php

namespace App\Http\Controllers\Admin;

use App\Models\SocialLink;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SocialLink\UpdateSocialLinkRequest;

class SocialLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('links.view') ;
        $links = SocialLink::all();
      
        return view('admin.social.index')->with('links', $links);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('links.create') ;
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('links.create') ;
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('links.view') ;
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('links.edit') ;
        $link = SocialLink::find($id);

        if (empty($link)) {
            Flash::error('Link not found');
            return redirect(route('links.index'));
        }

        return view('admin.social.edit')->with('link', $link);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSocialLinkRequest $request, $id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('links.edit') ;
        
        $link = SocialLink::find($id);
      
        if (empty($link)) {
            Flash::error('Link not found');
            return redirect(route('links.index'));
        }

        $link =  $link->update($request->all());;
        Flash::success('Link updated successfully.');

        return redirect(route('links.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('links.delete') ;
        //
    }
}
