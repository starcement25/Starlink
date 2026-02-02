<?php

namespace App\Http\Controllers\Tour\Admin;

use Laracasts\Flash\Flash;
use App\Models\PageContent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\DataTables\PageDataTable;
use App\Http\Controllers\Controller;

class PageContentController extends Controller
{
    public function index(PageDataTable $dataTable, Request $request)
    {
       return $dataTable->render('tour.page-data.index') ;
    }
    public function getPageContants($id)
    {
        $pageContents = PageContent::where('page_id', $id)->orderBy('show_order', 'ASC')->get() ;
        
        return view('tour.page-content.index')->with('contents', $pageContents)->with('pageId', $id);
    }
    public function getPageSpecificItem()
    {
        # code...
    }

    public function createPageContents($id)
    {
        $elementType = [
            '' => 'Select Type', 
            'select' => 'Select', 
            'searchable_select' => 'Searchable Select', 
            'text' => 'Text Box', 
            'label'=> 'Label', 
            'checkbox'=> 'Check Box', 
            'heading'=> 'Heading', 
            'rank'=> 'Ranking'
            ]  ;
        return view('tour.page-content.create')->with('pageId', $id)->with('elementType', $elementType);
    }

    public function editPageContents($id)
    {
        $item = PageContent::find($id) ;

        $elementType = [
                        '' => 'Select Type', 
                        'select' => 'Select', 
                        'searchable_select' => 'Searchable Select', 
                        'text' => 'Text Box', 
                        'label'=> 'Label', 
                        'checkbox'=> 'Check Box', 
                        'heading'=> 'Heading', 
                        'rank'=> 'Ranking'
                        ]  ;

        return view('tour.page-content.edit')->with('contentId', $id)->with('item', $item)->with('elementType', $elementType);
    }
    public function storePageContents(Request $request)
    {
        $request['element_name'] = Str::snake($request->element_name) ;
        $request['element_id'] =  $request['element_name'] ;

        if($request->element_type == "select" || $request->element_type = "searchable_select"){
            $request['element_value'] = json_encode(explode(',', $request->element_value)) ;
        }
        // return $request->all() ;

        $pageContent = PageContent::create($request->all());

        Flash::success('Content saved successfully.');

        return redirect(route('tour.page.list', ['id' => $request->page_id]));
    }
    public function updatePageContents(Request $request)
    {
        $content = PageContent::find($request->id);
        if(empty($content)){
            abort(404) ;
        }
        $content->update($request->all());
        Flash::success('Content updated successfully.');

        return redirect(route('tour.page.list', ['id'=>$content->page_id]));
         
    }
}
