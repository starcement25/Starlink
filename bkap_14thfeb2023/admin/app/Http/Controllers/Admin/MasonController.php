<?php

namespace App\Http\Controllers\Admin;

use Flash;
use App\Models\User;
use App\Models\Branch;
use App\Models\Reward;
use App\Models\MasonDealer;

use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\DataTables\MasonDataTable;
use Illuminate\Support\Facades\Hash;
use App\Repositories\MasonRepository;
use App\DataTables\MasonPointDataTable;
use App\Models\UserCatalogueRedeemtion;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Mason\CreateMasonRequest;
use App\Http\Requests\Mason\UpdateMasonRequest;
use App\Http\Requests\Mason\PointManupulateRequest;

class MasonController extends AppBaseController
{
    use HelperTrait;
    /** @var MasonRepository $masonRepository*/
    private $masonRepository;

    public function __construct(MasonRepository $masonRepo)
    {
        $this->masonRepository = $masonRepo;
    }

    /**
     * Display a listing of the Mason.
     */
    public function index(MasonDataTable $dataTable, Request $request)
    {
       return $dataTable->render('admin.masons.index') ;
                
    }

    /**
     * Show the form for creating a new Mason.
     */
    public function create()
    {
        $status = ['' => 'Select Status', '1'=> 'Married', '2'=> 'Un Married'];
        $branchArr = Branch::select('id', 'name')->pluck('name', 'id')->toArray();
        $branches = ['' => 'Select Branch'] + $branchArr ;
        $users = User::select('id', 'name')->whereIn('role', ['1'])->pluck('name', 'id')->toArray();
        $users = ['' => 'Select User'] + $users ;

        $dealerArr = User::select('id', 'name')->whereIn('role', ['3','4'])->pluck('name', 'id')->toArray();
        $dealers = ['' => 'Select User'] + $dealerArr ;

        return view('admin.masons.create')
                ->with('status', "")->with('maritalStatus', "")
                ->with('usersOption', $users)->with('userSelected', "")
                ->with('branchOption', $branches)->with('branchSelected', "") 
                ->with('dealerOption', $dealers)->with('dealerSelected', "") ;
      //  return view('admin.masons.create');
    }

    /**
     * Store a newly created Mason in storage.
     */
    public function store(CreateMasonRequest $request)
    {
        $input = $request->except(['dealers', 'aadhar_img']);
        $input['password'] = Hash::make($input['password']) ;
        $input['role'] = 2 ;
        $mason = User::create($input);
        if($request->has('aadhar_img')){
            $data = $this->uploadFile($request->file('aadhar_img'), 'aadhar') ;
            $mason->update(['aadhar_doc' => $data['path']]) ;
        }
        
        $this->addMasonDealers($mason->id, $request->dealers) ;

        Flash::success('Mason saved successfully.'); 

        return redirect(route('masons.index'));
    }

    /**
     * Display the specified Mason.
     */
    public function show($id)
    {
        $mason = User::find($id);

        if (empty($mason)) {
            Flash::error('Mason not found');

            return redirect(route('masons.index'));
        }

        return view('admin.masons.show')->with('mason', $mason);
    }

    /**
     * Show the form for editing the specified Mason.
     */
    public function edit($id)
    {
        $mason = User::find($id);

        if (empty($mason)) {
            Flash::error('Mason not found');

            return redirect(route('masons.index'));
        }
        $branchArr = Branch::select('id', 'name')->pluck('name', 'id')->toArray();
        $branches = ['' => 'Select Branch'] + $branchArr ;
        $users = User::select('id', 'name')->whereIn('role', ['1'])->pluck('name', 'id')->toArray();
        $users = ['' => 'Select User'] + $users ;

        $dealerArr = User::select('id', 'name')->whereIn('role', ['3','4'])->pluck('name', 'id')->toArray();
        $dealers = ['' => 'Select User'] + $dealerArr ;

        $dealersSelected = $mason->mason_dealers->pluck('dealer_id')->toArray() ;

        return view('admin.masons.edit')->with('mason', $mason)
                ->with('status', $mason->status)->with('maritalStatus', $mason->marital_status)
                ->with('usersOption', $users)->with('userSelected', $mason->created_by)
                ->with('branchOption', $branches)->with('branchSelected', $mason->branch_id) 
                ->with('dealerOption', $dealers)->with('dealerSelected', $dealersSelected) ;
                ;
        ;
    }

    /**
     * Update the specified Mason in storage.
     */
    public function update($id, UpdateMasonRequest $request)
    {
        $mason = User::find($id);

        if (empty($mason)) {
            Flash::error('Mason not found');

            return redirect(route('masons.index'));
        }
        // $input  = $request->except(['img']) ;
        $input = !empty($request->password) ? $request->except(['aadhar_img', 'dealers']) : $request->except(['password', 'aadhar_img', 'dealers']) ;
        
        if(array_key_exists('password',  $input)){
            $input['password'] = Hash::make($input['password']);
        }
       $mason->update($input);
         
        // Update Image If There Is Image.
         if(!empty($request->aadhar_img)){
            if(!empty($mason->aadhar_doc)){
                if(file_exists(public_path($mason->aadhar_img))){
                    unlink(public_path($mason->aadhar_img));
                }
            }
            
            $data = $this->uploadFile($request->file('aadhar_img'), 'aadhar') ;
            $mason->update(['aadhar_doc' => $data['path']]) ;
        }

        MasonDealer::whereIn('id', $mason->mason_dealers->pluck('id')->toArray())->delete();
        $this->addMasonDealers($mason->id, $request->dealers) ;

        Flash::success('Mason updated successfully.');

        return redirect(route('masons.index'));
    }

    /**
     * Remove the specified Mason from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $mason = User::find($id);

        if (empty($mason)) {
            Flash::error('Mason not found');

            return redirect(route('masons.index'));
        }

        $mason->delete($id);
        Flash::success('Mason deleted successfully.');

        return redirect(route('masons.index'));
    }

    // Point Add & Deduct For Mason
    public function saveManupulation(PointManupulateRequest $request)
    {   // 1 = Add , 2 = Deduct
        $action = ['1' => 'added', '2'=> 'deducted'];
        if($request->type == 1){
            Reward::create([
                'user_id' => $request->user,
                'point' => $request->point,
                'is_verified' => 1,
                'description' => 'Point add',
            ]);
        }else if($request->type == 2){
            UserCatalogueRedeemtion::create([
                'user_id'       => $request->user,
                'redeemed_point' => $request->point,
            ]);
        }
         // Update total user point.
         $this->updatePoint($request->user);

        Flash::success('Mason point '.$action[$request->type].' successfully.');
        return redirect(route('point.list')) ;
    }

    // All Masons Point List 
    public function showMasonsPoint(MasonPointDataTable $dataTable)
    {
       
       return  $dataTable->render('admin.masons.point-list');
    }

    // Show manupulate Form.
    public function showManupulateForm(Request $request, $id)
    {
       
        $user = User::with('by_created')->where('id', $id)->where('role', 2)->first();
        if(empty($user)){
            abort(404);
        }
       return view('admin.masons.point-manupulate', ['user'=> $user, 'userId'=> $id]) ;
    }

    public function addMasonDealers($masonId, $dealers)
    {
        if(count($dealers) > 0){
            foreach ($dealers as $key => $dealer) {
                MasonDealer::create([
                    'mason_id' => $masonId ,
                    'dealer_id' => $dealer ,
                ]);
            }
        }
    }
}
