<?php

namespace App\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Models\MasonCategory;
use App\Http\Controllers\Controller;
use App\DataTables\MasonCategoryDataTable;
use App\Http\Requests\MasonCategory\CreateMasonCategoryRequest;
use App\Http\Requests\MasonCategory\UpdateMasonCategoryRequest;

class MasonCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(MasonCategoryDataTable $dataTable, Request $request)
    {
       
        return $dataTable->render('admin.mason-category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.mason-category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateMasonCategoryRequest $request)
    {
        try {
            $item = MasonCategory::where('from_point', $request->from_point)->where('to_point', $request->to_point)->get() ;
            if(count($item) > 0){
                Flash::Error('These values are already exist');
                return redirect()->back()->withInput(); 
            }
            $masonCategory = MasonCategory::create($request->all()) ;
          
            Flash::success('Mason category saved successfully.');
            return redirect(route('mason-categories.index'));
        } 
        catch (\Exception $e) {
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('mason-categories.index'));
        }
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
        $masonCategory = MasonCategory::find($id);

        if (empty($masonCategory)) {
            Flash::error('Mason category not found');

            return redirect(route('MasonCategorys.index'));
        }

        return view('admin.mason-category.edit')->with('category', $masonCategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMasonCategoryRequest $request, $id)
    {
        $masonCategory = MasonCategory::find($id);

        if (empty($masonCategory)) {
            Flash::error('Mason category not found');
            return redirect(route('mason-categories.index'));
        }
       // Checking points are exist or not.
        $item = MasonCategory::where('from_point', $request->from_point)
                             ->where('to_point', $request->to_point)
                             ->whereNotIn('id', [$id])
                             ->get() ;
        
        if(count($item) > 0){
                Flash::Error('These values are already exist');
                return redirect()->back()->withInput(); 
        }
        $masonCategory->update($request->all()) ;
        Flash::success('MasonCategory updated successfully.');

        return redirect(route('mason-categories.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $masonCategory = MasonCategory::find($id);
        if (empty($masonCategory)) {
            Flash::error('Mason category not found');
            return redirect(route('mason-categories.index'));
        }
        $masonCategory->delete();

        Flash::success('Mason category deleted successfully.');
        return redirect(route('mason-categories.index'));
    }
    public function isMasonCategoryExist($request)
    {
        # code...
    }
}
