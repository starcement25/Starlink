<?php

namespace App\Http\Controllers\Admin;

use App\Models\StaticPage;
use Laracasts\Flash\Flash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\StaticPageDataTable;
use App\Http\Requests\Page\CreateStaticPageRequest;
use App\Http\Requests\Page\UpdateStaticPageRequest;

class StaticPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(StaticPageDataTable $dataTable, Request $request)
    {
        // $staticPages = staticPage::all();
        // return view('admin.staticPage.index')->with('staticPages', $staticPages);
       return $dataTable->render('admin.page.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       // return view('admin.page.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateStaticPageRequest $request)
    {
        // $input = $request->all();
        // $input['page_slug'] = Str::slug($input['page_name']);
        // $staticPage = StaticPage::create($input);
       
        // Flash::success('Page saved successfully.');

        // return redirect(route('pages.index'));
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
        $staticPage = StaticPage::find($id);

        if (empty($staticPage)) {
            Flash::error('Page not found');

            return redirect(route('staticPages.index'));
        }

        return view('admin.page.edit')->with('staticPage', $staticPage);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStaticPageRequest $request, $id)
    {
        $staticPage = StaticPage::find($id);

        if (empty($staticPage)) {
            Flash::error('Page not found');

            return redirect(route('pages.index'));
        }

        $staticPage =  $staticPage->update($request->all());;

        Flash::success('Page updated successfully.');

        return redirect(route('pages.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $staticPage = StaticPage::find($id);
        // if (empty($staticPage)) {
        //     Flash::error('Page not found');
        //     return redirect(route('pages.index'));
        // }

        // $staticPage->delete();

        // Flash::success('Page deleted successfully.');
        // return redirect(route('pages.index'));
    }
}
