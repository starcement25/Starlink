<?php

namespace App\Http\Controllers\Tour\Admin;

use App\Models\Page;
use Laracasts\Flash\Flash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
         /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pages = Page::all() ;
        return view('tour.admin-page.index')->with('pages', $pages);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tour.admin-page.create') ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $input['slug'] = Str::slug($input['name']);
        $data = Page::where('slug', $input['slug'])->get() ;
        if(count($data) > 0){
            Flash::error('Page already exist. Page name should be unique.');
            return redirect(route('tour.pages.index'));
        }
        $page = Page::create($input);
        Flash::success('Page saved successfully.');

        return redirect(route('tour.pages.index'));
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
        $page = Page::find($id);
        

        if (empty($page)) {
            Flash::error('Page not found');
            return redirect(route('tour.pages.index'));
        }

        return view('tour.admin-page.edit')->with('page', $page);
                                      
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
        $page = Page::find($id);

        if (empty($page)) {
            Flash::error('Page not found');
            return redirect(route('tour.pages.index'));
        }

        $page =  $page->update($request->all());;

        Flash::success('Page updated successfully.');

        return redirect(route('tour.pages.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
    }
}
