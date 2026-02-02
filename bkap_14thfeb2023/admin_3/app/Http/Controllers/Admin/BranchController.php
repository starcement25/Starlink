<?php

namespace App\Http\Controllers\Admin;

use Flash;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\DataTables\BranchDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Branch\CreateBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;

class BranchController extends Controller
{
       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BranchDataTable $dataTable, Request $request)
    {
        // $branches = Branch::all();
        // return view('admin.branch.index')   ->with('branches', $branches);
        
        return $dataTable->render('admin.branch.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.branch.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBranchRequest $request)
    {
        $input = $request->all();
       
        $Branch = Branch::create($input);
        Flash::success('Branch saved successfully.');

        return redirect(route('branch.index'));
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
        $branch = Branch::find($id);

        if (empty($branch)) {
            Flash::error('Branch not found');

            return redirect(route('branch.index'));
        }

        return view('admin.branch.edit')->with('branch', $branch);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBranchRequest $request, $id)
    {
        $branch = Branch::find($id);

        if (empty($branch)) {
            Flash::error('Branch not found');
            return redirect(route('branch.index'));
        }

        $branch =  $branch->update($request->all());;

        Flash::success('Branch updated successfully.');

        return redirect(route('branch.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $branch = Branch::find($id);
        if (empty($branch)) {
            Flash::error('Branch not found');
            return redirect(route('branch.index'));
        }

        $branch->delete();

        Flash::success('Branch deleted successfully.');
        return redirect(route('branch.index'));
    }
}

