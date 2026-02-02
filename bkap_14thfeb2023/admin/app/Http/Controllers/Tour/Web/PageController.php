<?php

namespace App\Http\Controllers\Tour\Web;

use App\Models\Page;
use App\Models\User;
use App\Models\PageData;
use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
   use HelperTrait;
   public function renderPage(Request $request, $slug)
   {
     
        $page     = Page::with('contents')->where('slug', $slug)->first() ;
        if(empty($page)){
         abort(404) ;
        }
        $contents = collect($page->contents);
        // return $page;
        $contents = $contents->sortBy('show_order') ;
        $contents = $contents->filter(function($data){
                           return $data->is_active == 1 ;
                     })->map(function($data){
                            return[
                                 'type'=> $data->element_type,
                                 'element' => $this->makeElement(
                                             $data->element_type, 
                                             $data->element_value, 
                                             $data->element_name, 
                                             $data->element_id,
                                             $data->title,
                                             $data->is_required,
                                 ),
                  ];
        });
       //return $contents;
       return view('tour.page.page-template')->with('contents', $contents)->with('pageId', $page->id) ;
      
   }
   public function getDealers()
   {
      $users = User::whereIn('role', ['3', '4'])->pluck('emp_code')->toArray() ;
      if(count($users) > 0){
         return response()->json(['success'=> true, 'data'=> $users], 200);
      }
      return response()->json(['success'=> false, 'data'=> []], 200);
   }
   public function saveData(Request $request)
   {
      $data = PageData::create([
         'page_id'=> $request->page_id,
         'page_data'=> json_encode($request->except(['_token', 'page_id'])),
      ]);
      return back()->with('Success','Registration saved successfully. Thanks!' );
   }

   public function getDealerByCode(Request $request)
   {
      $user = User::with('branch')->where('emp_code', $request->dealerCode)->whereIn('role', ['3','4'])->first() ;
      if(empty($user)){
         return response()->json(['success'=>false, 'data'=> []], 200);
      }
      return response()->json(['success'=>true, 'data'=> $user], 200);
   }
}
