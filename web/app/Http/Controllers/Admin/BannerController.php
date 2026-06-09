<?php

namespace App\Http\Controllers\Admin;

use App\Models\Zone;
use App\Models\Banner;
use Laracasts\Flash\Flash;
use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\DataTables\BannerDataTable;
use App\Http\Controllers\Controller;


class BannerController extends Controller
{
    use HelperTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BannerDataTable $dataTable, Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('banners.view') ;
       // dd($dataTable);
        return $dataTable->render('admin.banner.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('banners.create') ;
        $zones = Zone::where('status', '1')->orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        return view('admin.banner.create')->with('zoneOption', $zones)->with('zoneOptionSelected', "") ;
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('banners.create') ;
        try {
            $request->validate([
                'title'=> 'required|max:250',
                'description'=> 'required|max:500',
                'zone_id'     => 'required|array',
                'zone_id.*'   => 'exists:zones,id',
            ]);

        //dd($request);

            $input = $request->except(['image', 'zone_id']) ;
            $banner = Banner::create($input) ;
           
            if($request->has('image')){
                $data = $this->uploadFile($request->file('image'), 'banners') ;
                $banner->update(['img' => $data['path']]) ;
            }
             // Save zones (banners_with_zones table)
            if ($request->filled('zone_id')) {
                $banner->zones()->sync($request->zone_id);
            }

            Flash::success('Banner saved successfully.');
            return redirect(route('banners.index'));
        } 
        catch (\Exception $e) {
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('banners.index'));
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('banners.view') ;
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('banners.edit') ;   
        $banner = Banner::find($id);

        if (empty($banner)) {
            Flash::error('Banner not found');
            return redirect(route('banners.index'));
        }

        $zones = Zone::where('status', '1')->orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        $zoneOptionSelected = $banner->zones->pluck('id')->toArray();
        return view('admin.banner.edit')->with('banner', $banner)->with('zoneOption', $zones)->with('zoneOptionSelected', $zoneOptionSelected)
           ;
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('banners.edit') ;
        $request->validate([
            'title'=> 'required|max:250',
            'description'=> 'required|max:500',
            'zone_id'     => 'required|array',
            'zone_id.*'   => 'exists:zones,id',
        ]);

        $banner = Banner::find($id);
      
        if (empty($banner)) {
            Flash::error('Banner not found');
            return redirect(route('banners.index'));
        }
       
        $input = $request->except(['image', 'zone_id']) ;
        $result =  $banner->update($input);
        if(!empty($request->image)){
            if(file_exists(public_path($banner->img))){
                unlink(public_path($banner->img));
            }
            $data = $this->uploadFile($request->file('image'), 'banners') ;
            $banner->update(['img' => $data['path']]) ;
        }
        $banner->zones()->sync($request->zone_id ?? []);
        Flash::success('Banner updated successfully.');

        return redirect(route('banners.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('banners.delete') ;
        $banner = Banner::find($id);
        if (empty($banner)) {
            Flash::error('Banner not found');
            return redirect(route('banners.index'));
        }
        if(!empty($banner->image) && is_file(public_path($banner->img))){
            unlink(public_path($banner->img));
        }
        $banner->zones()->detach();
        $banner->delete();

        Flash::success('Banner deleted successfully.');
        return redirect(route('banners.index'));
    }
}
