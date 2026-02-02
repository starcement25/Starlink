<?php

namespace App\DataTables;

use App\Models\User;
use App\Models\State;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MasonDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('by_created.name', function($user){
                return $user->by_created->name ?? "" ;
            })
            ->editColumn('branch.name', function ($user) {
                return $user->branch->name ?? "";
            })
            ->editColumn('branch.name', function ($user) {
                return $user->branch->name ?? "";
            })
            ->addColumn('action', function ($user) {
                return '<form method="POST" action="'.route('masons.destroy', ['mason'=> $user->id]).'">
                        '.csrf_field().'
                            <input name="_method" type="hidden" value="DELETE">
                            <div class="btn-group">
                            <a href="'.route('masons.edit', ['mason'=> $user->id]).'" class="btn btn-default btn-xs">
                                <i class="far fa-edit"></i>
                            </a>
                           <!--  <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure?\')">
                                <i class="far fa-trash-alt"></i>
                            </button> -->
                        </div>';
            })
            ->editColumn('states.state_name',function($user){
                return $user->states->state_name ?? "";
            })
            ->editColumn('aadhaar_doc',function($user){
                //return $_SERVER['SERVER_NAME'];
                //return $user->aadhar_doc ?? "";
                $baseUrl=$_SERVER['SERVER_NAME'];
                $baseUrl="https://".$baseUrl."/public/aadhaar/";
                //$base64Format="data:image/jpeg;base64,";
                //$image=$base64Format.base64_encode(file_get_contents($baseUrl.$user->aadhaar_doc));
                $image=$baseUrl.$user->aadhaar_doc;
                //return $image;
                return $user->aadhaar_doc == null ? "No Aadhar Found." : "<img src='".$image."' height='100' width='100' >";
            })
            ->editColumn('voter_number',function($user){
                
                //return $image;
                return ($user->branch->state->is_voter_require ?? 0) == State::VOTER_REQUIRE_YES ? (empty($user->voter_number) ? "No Voter Number Found." : $user->voter_number) : "N/A";
                
            })
            ->editColumn('voter_doc',function($user){
                
                $baseUrl= $_SERVER['SERVER_NAME'];
                $baseUrl= "https://".$baseUrl."/public/";
                $basePath = dirname(base_path())."/public/";
                return (!empty($user->voter_doc) && file_exists($basePath.$user->voter_doc)) ? "<img src='".($baseUrl.$user->voter_doc)."' height='100' width='100' >" : (($user->branch->state->is_voter_require ?? 0) == State::VOTER_REQUIRE_YES ? "No Voter Found." : "N/A");
                
            })
            // ->editColumn('aadhaar_doc_image',function($user){
            //     //return $user->aadhar_doc ?? "";
            //     $baseUrl=$_SERVER['SERVER_NAME'];
            //     $baseUrl="https://".$baseUrl."/public/aadhaar/";
                
            //     //return $image;
            //     return $user->aadhaar_doc == null ? "" : "<a href='".$baseUrl.$user->aadhaar_doc."'>".$baseUrl.$user->aadhaar_doc."</a>";
            // })
            ->editColumn('te_linked.name',function($user){
                return $user->te_linked->name ?? "";
            })
            ->editColumn('marital_status',function($user){
                return $user->marital_status == 1 ? "Married" : "Unmarried";
            })
            ->addColumn('te_linked.profile_pic',function($user){
                // $baseUrl=$_SERVER['SERVER_NAME'];
                // $baseUrl="https://".$baseUrl."/public/profile/";
                $te_linked_profile_pic = $user->te_linked->profile_pic ?? null;
                return $te_linked_profile_pic == null ? "No Image Found." : "<img src='".$te_linked_profile_pic."' height='100' width='100' >";
            })
            ->editColumn('te_linked.image',function($user){
                $te_linked_image = $user->te_linked->profile_pic ?? null;
                return $te_linked_image == Null ? "" : "<a href='".$user->te_linked->profile_pic."'>".$user->te_linked->profile_pic."</a>";
            })
            ->editColumn('profile_pic',function($user){
                return $user->profile_pic == null ? "No Image Found." : "<img src='".$user->profile_pic."' height='100' width='100' >";
            })
            // ->addColumn('te_linked.address',function($user){
            //     return $user->address1.','.$user->address2.','.$user->city.','.$user->district.','.$user->state.','.$user->country.' - '.$user->pincode;
            // })
            ->editColumn('profile',function($user){
                return $user->profile_pic == Null ? "" : "<a href='".$user->profile_pic."'>".$user->profile_pic."</a>";
            })
            ->editColumn('created_at', function($user){ 
                $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $user->created_at); 
                return $formatedDate; 
            })
            ->editColumn('branch.zone.name', function($user){ 
                return $user->branch->zone->name ?? ""; 
            })
            ->addColumn('mason_dealers',function($user){
                $i=0;
                $dealerName="";
                foreach($user->mason_dealers as $val)
                {
                    if($val->dealer != null){
                        if($i==0)
                        {
                            $dealerName= $val->dealer->name;
                        }
                        else
                        {
                            $dealerName.= ",".$val->dealer->name;
                        } 
                    }
                    $i++;
                }
                return $dealerName;
            })
            ->addColumn('mason_dealers_code',function($user){
                $i=0;
                $dealerCode="";
                foreach($user->mason_dealers as $val)
                {
                    if($val->dealer != null){
                        if($i==0)
                        {
                            $dealerCode= $val->dealer->emp_code;
                        }
                        else
                        {
                            $dealerCode.= ",".$val->dealer->emp_code;
                        } 
                    }
                    $i++;
                }
                return $dealerCode;
            })
            ->editColumn('status',function($user){
                return $user->status == 1 ? "Active" : "Disabled";
            })
            ->editColumn('disable_reason',function($user){
                return $user->status == 0 ? $user->disable_reason : "";
            })
            ->editColumn('disable_date_time',function($user){
                return $user->disable_date_time != null ? Carbon::parse($user->disable_date_time)->toDateString() : "";
            })
            ->editColumn('login_status',function($user){
                return $user->login_status == 1 ? "Y" : "N";
            })
            ->addColumn('dealer_linked_name',function($user){
                $dealers="";
                $i = 0;
                foreach($user->mason_dealers as $val)
                {
                    if($i!=0)
                    {
                        $dealers.=", ";
                    }
                    $dealers.=$val->dealer->name ?? "";
                    $i++;
                }
                return $dealers;
            })
            ->addColumn('dealer_linked_code',function($user){
                $dealerCodes="";
                $i = 0;
                foreach($user->mason_dealers as $val)
                {
                    if($i!=0)
                    {
                        $dealerCodes.=", ";
                    }
                    $dealerCodes.=$val->dealer->emp_code ?? "";
                    $i++;
                }
                return $dealerCodes;
            })
        ->rawColumns(['action','te_linked.name','aadhaar_doc','aadhaar_doc_image','voter_number','voter_doc','te_linked.profile_pic','te_linked.image','profile_pic','profile']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Mason $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
    {
        $loggedUser=Auth::user();
        if($loggedUser->role > 6)
        {
            $allocated_branches=json_decode($loggedUser->allocated_branches);
            $model = $model->newQuery()->with('mason_dealers')->with('te_linked')->with('states')->with(['branch', 'branch.zone'])->with('by_created')->where('role', 2)->whereIn('branch_id',$allocated_branches);
            // $temp = $model->newQuery()->with('mason_dealers')->with('te_linked')->with('states')->with('branch')->with('by_created')->where('role', 2)->whereIn('branch_id',$allocated_branches)->orderBy('id', 'DESC');
            // return Datatables::of($temp)->make(true);
        }
        else
        {
            $model = $model->newQuery()->with('mason_dealers')->with('te_linked')->with('states')->with(['branch', 'branch.zone'])->with('by_created')->where('role', 2);
            // $temp = $model->newQuery()->with('mason_dealers')->with('te_linked')->with('states')->with('branch')->with('by_created')->where('role', 2)->orderBy('id', 'DESC');
            // return Datatables::of($temp)->make(true);
        }
        
        if($this->filterBy != null && !empty((string)$this->fromDate) && !empty((string)$this->toDate) && $this->fromDate <= $this->toDate)
        {
            if($this->filterBy == 2)
            {
                $model = $model->whereDate('disable_date_time', '>=', $this->fromDate)->whereDate('disable_date_time', '<=', $this->toDate);
            }
            else
            {
                $model = $model->whereDate('created_at', '>=', $this->fromDate)->whereDate('created_at', '<=', $this->toDate);
            }
        }
        // dd($this->statusFilter);
        if($this->statusFilter != null && $this->filterBy != 2)
        {
            $model = $model->where('status', $this->statusFilter);
        }
        return $model->orderBy('id', 'DESC');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
        ->columns($this->getColumns())
        ->minifiedAjax()
      //  ->addAction(['width' => '120px', 'printable' => false])
        ->parameters([
            'dom'       => 'frtip',
            'stateSave' => true,
            'order'     => [[0, 'desc']],
            'buttons'   => ['excel', 'csv'],
                // Enable Buttons as per your need
//                    ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner',],
//                    ['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner',],
//                    ['extend' => 'print', 'className' => 'btn btn-default btn-sm no-corner',],
//                    ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner',],
//                    ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner',],
          
        ]);
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
           
            Column::make('id')->searchable(false)->visible(false),
            Column::make('name'),
            Column::make('profile_pic')->title('Image')->exportable(false)->searchable(false),
            Column::make('profile')->title('Image')->visible(false)->searchable(false),
            Column::make('address1'),
            Column::make('address2'),
            Column::make('city'),
            Column::make('district'),
            Column::make('state')->title('State'),
            Column::make('country'),
            Column::make('pincode'),
            Column::make('aadhaar_no'),
            Column::make('aadhaar_doc')->exportable(false)->searchable(false),
            Column::make('voter_number'),
            Column::make('voter_doc')->exportable(false)->searchable(false),
            // Column::make('aadhaar_doc_image')->title('aadhaar_doc')->visible(false)->searchable(false),
            Column::make('dob'),
            Column::make('phone')->title('Phone Number'),
            Column::make('marital_status'),
            Column::make('spouse_name'),
            Column::make('spouse_dob'),
            Column::make('branch.name'),
            Column::make('branch.zone.name')->title('Zone'),
            Column::make('by_created.name')->title('Created By'),
            Column::make('te_linked.name')->title('Linked BDE'),
            Column::make('te_linked.profile_pic')->title('BDE Image')->exportable(false)->searchable(false),
            Column::make('te_linked.image')->title('BDE Image')->visible(false)->searchable(false),
            // Column::make('te_linked.address')->title('BDE Address'),
            Column::make('mason_dealers')->title('Linked Dealer')->visible(false),
            Column::make('mason_dealers_code')->title('Dealer Code')->visible(false),
            Column::make('points'),
            Column::make('status')->title("Contractor Status"),
            Column::make('disable_reason')->title("Disable Reason"),
            Column::make('disable_date_time')->title("Disable Date"),
            Column::make('login_status'),
            Column::make('last_login_date_time'),
            Column::make('login_device_type')->title('Device Type'),
            Column::make('login_device_name')->title('Device Name'),
            Column::make('app_version')->title('App Version'),
            Column::make('dealer_linked_code')->title('Linked Dealer Code'),
            Column::make('dealer_linked_name')->title('Linked Dealer Name'),
            Column::make('created_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Mason_' . date('YmdHis');
    }
}
