<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Content;
class ContentController extends Controller
{
    function edtiContentAdminView()
    {
        return view('superadmin/edit-content');
    }
    function contentSave(Request $req)
    {
        $req->validate([
            'title' => 'required',
            'content' => 'required',
            'slug' =>  'required',
           
        ]);
       
        $content = Content::where('slug',$req->slug)->first();
        if(!$content)
        {
            $content = new Content;
        }
        $content->title = $req->title;
        $content->content = $req->content;
        if($req->hasFile('image'))
        {
            $name = $req->slug.".jpg";
            $path = public_path('/web/images');
            $req->file('image')->move($path, $name);
            $content->img = $name;
        }
        $content->save();

        return back()->with(['success'=> $content->title." Save Successfully"]);

    }
}
