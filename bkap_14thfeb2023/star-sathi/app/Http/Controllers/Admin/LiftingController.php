<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Reward;
use App\Models\Lifting;
use App\Models\Product;
use Laracasts\Flash\Flash;
use App\Traits\HelperTrait;
use App\Models\MasonLifting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\LiftingDataTable;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
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
        // Get Users having Role Mason = 2.
        $users    = User::where('role', 2)->orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        
        $products = Product::orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        $users    = ['' => 'Select Mason'] + $users ;

        $dealersArr    = User::whereIn('role', ['1', '3', '4'])->orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        $dealers    = ['' => 'Select TE/Dealer'] + $dealersArr ;
       
        $products = ['' => 'Select User'] + $products ;
      
        return view('admin.lifting.create')
                ->with('userOption', $users)->with('userSelected', "")
                ->with('teOption', $dealers)->with('teSelected', "")
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
            DB::beginTransaction();
            $request['lifting_date'] = date('d-m-Y', strtotime($request->lifting_date));
          
            $lifting = Lifting::create($request->except(['img', 'mason_id'])) ;
            
            if($request->has('img')){
                $data = $this->uploadFile($request->file('img'), 'liftings') ;
                $lifting->update(['img' => $data['path']]) ;
            }
            
            // Add Mason Lifting.
            $masonLifting =  MasonLifting::create([
                'mason_id'=> $request->mason_id,
                'lifting_id'=> $lifting->id,
            ]);

            // Add Rewards
            Reward::create([
                            'lifting_id'  => $lifting->id, 
                            'user_id'     => $request->mason_id, 
                            'bag'         => $lifting->qty, 
                            'point'       => $this->getPoint($lifting->product_id, $lifting->qty),
                            'is_verified' => 0 ,
                            ]) ;
            DB::commit();
            Flash::success('Lifting saved successfully.');
            return redirect(route('liftings.index'));
        } 
        catch (\Exception $e) {
            DB::rollback();
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
            $lifting['lifting_date'] = date('Y-m-d', strtotime($lifting['lifting_date']));
            // Get Users having Role Mason = 2.
            $users    = User::where('role', 2)->orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
            $products = Product::orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
            $users    = ['' => 'Select User'] + $users ;
            $products = ['' => 'Select User'] + $products ;

            $dealersArr    = User::whereIn('role', ['1', '3', '4'])->orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
            $dealers    = ['' => 'Select TE/Dealer'] + $dealersArr ;


            return view('admin.lifting.edit')->with('lifting', $lifting)
                        ->with('userOption', $users)->with('userSelected', $lifting->mason_user->mason_id)
                        ->with('productOption', $products)
                        ->with('productSelected',  $lifting->product_id)
                        ->with('teOption', $dealers)->with('teSelected', $lifting->user_id);
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
        try {
            
            $lifting = Lifting::find($id);

            if (empty($lifting)) {
                Flash::error('Lifting not found');
                return redirect(route('liftings.index'));
            }
            $request['lifting_date'] = date('d-m-Y', strtotime($request->lifting_date));
            DB::beginTransaction();
            $input  = $request->except(['img', 'mason_id']) ;
            $result = $lifting->update($input);

            // Update Image
            if(!empty($request->img)){
                if(file_exists(public_path($lifting->img))){
                    unlink(public_path($lifting->img));
                }
                $data = $this->uploadFile($request->file('img'), 'liftings') ;
                $lifting->update(['img' => $data['path']]) ;
            }

            //Update or Create Reward Details.
            Reward::updateOrCreate(
                ['id' => $lifting->reward->id ?? null],
                [
                    'lifting_id'  => $lifting->id, 
                    'user_id'     => $request->user_id, 
                    'bag'         => $lifting->qty, 
                    'point'       => $this->getPoint($lifting->product_id, $lifting->qty),
                    'is_verified' => $lifting->reward->is_verified ?? 0 ,
                ]);

            //Update or Create Mason Liftings Details.
            MasonLifting::updateOrCreate(
                ['id' => $lifting->mason_user->id ?? null],
                [
                    'lifting_id'  => $lifting->id, 
                    'mason_id'     => $request->mason_id, 
                    
                ]);

            // Update total user point.
            $this->updatePoint($request->mason_id);
            
            DB::commit();

            Flash::success('Lifting updated successfully.');
            return redirect(route('liftings.index'));

        } catch (\Exceotion $e) {
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('liftings.index'));
        }
      
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
            
            // Update total user point.
            $this->updatePoint($lifting->user_id);
            
            Flash::success('Lifting deleted successfully.');
            return redirect(route('liftings.index'));
        
        } catch (\Throwable $e) {
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('liftings.index'));
        }
       
    }
    public function verifyLiftings(Request $request)
    {
     
        $users    = User::where('role', 2)->orderBy('name', 'DESC')->get();
        $user = $request->user ;

        if($request->expectsJson()){
           $liftings = Lifting::with('product')->with('reward')->whereIn('id', function($q) use($user){
                $q->select('lifting_id')->from('rewards')->where('user_id', $user);
           })
           // $liftings = Lifting::with('product')->with('reward')->where('user_id', $request->user)
            ->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$request->fromDate, $request->toDate]);

            return DataTables::eloquent($liftings)
            ->setRowId(function ($lifting) {
                return $lifting->id;
            })
            ->addColumn('status', function ($lifting) {
               $status = $lifting->reward->is_verified ? '<span class="badge badge-success"> Verified</span>' : '' ;
            
               return $status  ;
            })
            ->addColumn('action', function ($lifting) {
                $action = $lifting->reward->is_verified ? '<span class="badge badge-success"> Verified</span>' : '' ;
               return $lifting->reward->is_verified ? '<input type="checkbox"  id="switch'.$lifting->id.'" value="'.$lifting->reward->id.'" onchange="changeStatus('.$lifting->id.')" checked>' 
               : '<input type="checkbox"  id="switch'.$lifting->id.'" value="'.$lifting->reward->id.'" onchange="changeStatus('.$lifting->id.')">';
            })
            ->rawColumns(["action", "status"])
            ->toJson();
         //   return $liftings;
          
        }
        return view('admin.lifting.verify-lifting')->with('users', $users);
    }

    // Update Reward table is verified column in single. 
    public function updateRewardStatus(Request $request)
    {
       $lifting = Lifting::find($request->liftingId) ;
       if(empty($lifting)){
            return response()->json(['status'=>false , 'message'=> 'No lifting records found'], 200);
       }
       $reward = Reward::find($lifting->reward->id) ;
       if(empty($reward)){
            return response()->json(['status'=>false , 'message'=> 'No rewards records found to that lifting.'], 200);
       }
       $reward->update(['is_verified' => ($request->updateType == "true" ? 1 : 0)]);

       // Update total user point.
       $this->updatePoint($lifting->reward->user_id);

       $extra =  $request->updateType == "true" ? '<span class="badge badge-success"> Verified</span>': '';
      
       return response()->json(['status'=>true ,'extra'=> $extra, 'message'=> 'Liftings verified successfully.'], 200);

    }

    // Update Reward table is verified column in bulk. 
    public function updateBulkRewardStatus(Request $request)
    { 
        $liftings = Lifting::with('product')->with('reward')->where('user_id', $request->user)
                    ->whereBetween(DB::raw("(STR_TO_DATE(lifting_date,'%d-%m-%Y'))"), [$request->fromDate, $request->toDate])->get();
        $rewardIds = [];
        foreach ($liftings as $key => $lifting) {
            if($lifting->reward->id){
                $rewardIds[] = $lifting->reward->id ;
            }
           
        }
        if(count($rewardIds) > 0){
            Reward::whereIn('id', $rewardIds)->update(['is_verified' => 1]) ;
            
            // Update total user point.
            $this->updatePoint($request->user);

            return response()->json(['status'=> true , 'message'=> 'Liftings verified successfully.'], 200);
        }

        return response()->json(['status'=> false , 'message'=> 'No rewards records found for the liftings.'], 200);
    }

    // User Point Report Generation.
    public function masonReport(Request $request)
    {
        
    }
}
