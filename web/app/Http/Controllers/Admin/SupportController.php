<?php

namespace App\Http\Controllers\Admin;

use App\Models\Support;
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('supports.view');
         return $dataTable->render('admin.support.index');
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('supports.create');
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('supports.create');
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('supports.view');
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('supports.edit');
        $data = ['1' => 'Not Delivered', '2'=>'Defective Product'] ;
        $support = Support::with(['order', 'order.catalogue', 'order.user'])->findOrFail($id);
      //  return $support;
        $support['support_name'] = $data[$support->support_type] ;
        return view('admin.support.edit', ['support'=> $support]) ;
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('supports.edit');
        $support = Support::findOrFail($id) ;

        $support->update(['status'=> $request->status]) ;        
        
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
        \Helper::checkIsUserAuthorizeToPerformTheTask('supports.delete');
        //
    }
}
