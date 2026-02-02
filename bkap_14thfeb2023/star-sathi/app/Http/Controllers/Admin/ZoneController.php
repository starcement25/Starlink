<?php

namespace App\Http\Controllers\Admin;

use App\Models\Zone;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\DataTables\ZoneDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Zone\CreateZoneRequest;
use App\Http\Requests\Zone\UpdateZoneRequest;


class ZoneController extends Controller
{
       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ZoneDataTable $dataTable, Request $request)
    {
       
        return $dataTable->render('admin.zone.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.zone.create')->with('selected', "");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateZoneRequest $request)
    {
        try {
            $zone = Zone::create($request->all()) ;
          
            Flash::success('Zone saved successfully.');
            return redirect(route('zones.index'));
        } 
        catch (\Exception $e) {
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('zones.index'));
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
        $Zone = Zone::find($id);

        if (empty($Zone)) {
            Flash::error('Zone not found');

            return redirect(route('zones.index'));
        }

        return view('admin.zone.edit')->with('zone', $Zone)->with('selected', $Zone->status);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateZoneRequest $request, $id)
    {
        $zone = Zone::find($id);

        if (empty($zone)) {
            Flash::error('Zone not found');
            return redirect(route('zones.index'));
        }
        $zone->update($request->all()) ;
        Flash::success('Zone updated successfully.');

        return redirect(route('zones.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $zone = Zone::find($id);
        if (empty($zone)) {
            Flash::error('Zone not found');
            return redirect(route('zones.index'));
        }
        $zone->delete();

        Flash::success('Zone deleted successfully.');
        return redirect(route('zones.index'));
    }
}
