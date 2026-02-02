<?php

namespace App\Http\Controllers\Admin;

use App\Models\Catalogue;
use Laracasts\Flash\Flash;
use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\Models\MasonCategory;
use App\Http\Controllers\Controller;
use App\DataTables\CatalogueDataTable;
use App\Http\Requests\Catalogue\CreateCatalogueRequest;
use App\Http\Requests\Catalogue\UpdateCatalogueRequest;

class CatalogueController extends Controller
{
    use HelperTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CatalogueDataTable $dataTable, Request $request)
    {
        return $dataTable->render('admin.catalogue.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $masonCategory = MasonCategory::orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        $masonCategory = ['' => 'Select Mason Category'] + $masonCategory;
        
        return view('admin.catalogue.create')->with('categoryOption', $masonCategory)
            ->with('categorySelected', "")
            ->with('statusSelected',"");
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCatalogueRequest $request)
    {
        try {
            $catalogue = Catalogue::create($request->except(['image'])) ;
            
            if($request->has('image')){
                $data = $this->uploadFile($request->file('image'), 'catalogues') ;
                $catalogue->update(['image' => $data['path']]) ;
            }
            Flash::success('Catalogue saved successfully.');
            return redirect(route('catalogues.index'));
        } 
        catch (\Exception $e) {
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('catalogues.index'));
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
        $catalogue = Catalogue::find($id);

        if (empty($catalogue)) {
            Flash::error('catalogue not found');
            return redirect(route('catalogues.index'));
        }

        $masonCategory = MasonCategory::orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        $masonCategory = ['' => 'Select Mason Category'] + $masonCategory;

        return view('admin.catalogue.edit')->with('catalogue', $catalogue)
                ->with('categoryOption', $masonCategory)
                ->with('categorySelected', $catalogue->mason_category_id)
                ->with('statusSelected', $catalogue->status);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCatalogueRequest $request, $id)
    {
        $catalogue = catalogue::find($id);
        
        if (empty($catalogue)) {
            Flash::error('Catalogue not found');
            return redirect(route('catalogues.index'));
        }
       
        $input = $request->except(['image']) ;
        $result =  $catalogue->update($input);
        if(!empty($request->image)){
            if(file_exists(public_path($catalogue->image))){
                unlink(public_path($catalogue->image));
            }
            $data = $this->uploadFile($request->file('image'), 'catalogues') ;
            $catalogue->update(['image' => $data['path']]) ;
        }
        Flash::success('Catalogue updated successfully.');

        return redirect(route('catalogues.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $catalogue = Catalogue::find($id);
        if (empty($catalogue)) {
            Flash::error('catalogue not found');
            return redirect(route('catalogues.index'));
        }
        if(!empty($catalogue->image) && file_exists(public_path($catalogue->image))){
            unlink(public_path($catalogue->image));
        }
        $catalogue->delete();

        Flash::success('catalogue deleted successfully.');
        return redirect(route('catalogues.index'));
    }
}
