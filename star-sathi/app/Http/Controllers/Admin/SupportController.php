<?php

namespace App\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserCatalogueRedeemtion;
use App\DataTables\SupportMasterDataTable;
use App\Http\Requests\Support\UpdateSupportRequest;

class SupportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SupportMasterDataTable $dataTable)
    {
         return $dataTable->render('admin.support.index');
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        $redeemtion = UserCatalogueRedeemtion::with('catalogue')->with('user')->findOrFail($id);
     //  return $redeemtion ;
        return view('admin.support.edit', ['redeemtion'=> $redeemtion]) ;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSupportRequest $request, $id)
    {
       
        $redeemtion = UserCatalogueRedeemtion::findOrFail($id) ;
        $redeemtion->update($request->all()) ;
        
        Flash::success('Updated successfully.');
        return redirect(route('supports.index')) ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
