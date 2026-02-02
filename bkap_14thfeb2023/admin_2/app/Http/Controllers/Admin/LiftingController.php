<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Lifting;
use App\Models\Product;
use Laracasts\Flash\Flash;
use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\DataTables\LiftingDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lifting\CreateLiftingRequest;
use App\Http\Requests\Lifting\UpdateLiftingRequest;

class LiftingController extends Controller
{
    use HelperTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LiftingDataTable $dataTable, Request $request)
    {
        //    $liftings =  Lifting::with('user')->with('Lifting')->orderByRaw('lifting.id DESC')->get();
        //    return view('admin.lifting.index', ['liftings'=> $liftings]);

        return $dataTable->render('admin.lifting.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users    = User::orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        $products = Product::orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        $users    = ['' => 'Select User'] + $users ;
        $products = ['' => 'Select User'] + $products ;
      
        return view('admin.lifting.create')
                ->with('userOption', $users)->with('userSelected', "")
                ->with('productOption', $products)->with('productSelected', "");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateLiftingRequest $request)
    {   
        try {
            $lifting = Lifting::create($request->except(['img'])) ;
            
            if($request->has('img')){
                $data = $this->uploadFile($request->file('img'), 'liftings') ;
                $lifting->update(['img' => $data['path']]) ;
            }
            Flash::success('Lifting saved successfully.');
            return redirect(route('liftings.index'));
        } 
        catch (\Exception $e) {
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('liftings.index'));
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
        try {
            $lifting = Lifting::with('user')->find($id);
            if (empty($lifting)) {
                Flash::error('Lifting not found');
                return redirect(route('liftings.index'));
            }

            $users    = User::orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
            $products = Product::orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
            $users    = ['' => 'Select User'] + $users ;
            $products = ['' => 'Select User'] + $products ;

            return view('admin.lifting.edit')->with('lifting', $lifting)
                        ->with('userOption', $users)->with('userSelected', $lifting->user_id)
                        ->with('productOption', $products)->with('productSelected',  $lifting->product_id);;
        } 
        catch (\Exception $e) {
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('liftings.index'));
        }
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLiftingRequest $request, $id)
    {
        $lifting = Lifting::find($id);

        if (empty($lifting)) {
            Flash::error('Lifting not found');
            return redirect(route('liftings.index'));
        }
       
        $input = $request->except(['img']) ;
        $result =  $lifting->update($input);
        if(!empty($request->img)){
            if(file_exists(public_path($lifting->img))){
                unlink(public_path($lifting->img));
            }
            $data = $this->uploadFile($request->file('img'), 'liftings') ;
            $lifting->update(['img' => $data['path']]) ;
        }
        Flash::success('Lifting updated successfully.');

        return redirect(route('liftings.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $lifting = Lifting::find($id);
            if (empty($lifting)) {
                Flash::error('Lifting not found');
                return redirect(route('liftings.index'));
            }

            if(!empty($lifting->img) && file_exists(public_path($lifting->img))){
                unlink(public_path($lifting->img));
            }
           
            $lifting->delete();
            
            Flash::success('Lifting deleted successfully.');
            return redirect(route('liftings.index'));
        
        } catch (\Throwable $e) {
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('liftings.index'));
        }
       
    }
}
