<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\User;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class MasonDashboardDataTable extends DataTable
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
            ->addColumn('branch.name', function ($user) {
                return $user->branch->name ?? "";
            })
            ->addColumn('branch.zone.name', function ($user) {
                return $user->branch->zone->name ?? "";
            })
            ->editColumn('states.state_name',function($user){
                return $user->states->state_name ?? "";
            })
            ->editColumn('aadhaar_doc',function($user){
                //return $_SERVER['SERVER_NAME'];
                //return $user->aadhar_doc ?? "";
                $baseUrl=$_SERVER['SERVER_NAME'];
                $baseUrl="http://".$baseUrl."/public/aadhaar/";
                //$base64Format="data:image/jpeg;base64,";
                //$image=$base64Format.base64_encode(file_get_contents($baseUrl.$user->aadhaar_doc));
                $image=$baseUrl.$user->aadhaar_doc;
                //return $image;
                return $user->aadhaar_doc == null ? "No Aadhar Found." : "<img src='".$image."' height='100' width='100' >";
            })
            ->editColumn('aadhaar_doc_image',function($user){
                //return $user->aadhar_doc ?? "";
                $baseUrl=$_SERVER['SERVER_NAME'];
                $baseUrl="https://".$baseUrl."/public/aadhaar/";
                
                //return $image;
                return $user->aadhaar_doc == null ? "" : "<a href='".$baseUrl.$user->aadhaar_doc."'>".$baseUrl.$user->aadhaar_doc."</a>";
            })
            ->addColumn('te_linked.name',function($user){
                return $user->te_linked->name ?? "";
            })
            ->editColumn('marital_status',function($user){
                return $user->marital_status == 1 ? "Married" : "Unmarried";
            })
            ->addColumn('te_linked.profile_pic',function($user){
                // $baseUrl=$_SERVER['SERVER_NAME'];
                // $baseUrl="https://".$baseUrl."/public/profile/";
                return $user->te_linked->profile_pic == null ? "No Image Found." : "<img src='".$user->te_linked->profile_pic."' height='100' width='100' >";
            })
            ->editColumn('te_linked.image',function($user){
                return $user->te_linked->profile_pic == Null ? "" : "<a href='".$user->te_linked->profile_pic."'>".$user->te_linked->profile_pic."</a>";
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
                return $user->status == 1 ? "Active" : "Inactive";
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
        ->rawColumns(['te_linked.name','aadhaar_doc','aadhaar_doc_image','te_linked.profile_pic','te_linked.image','profile_pic','profile']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Mason $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->with('mason_dealers')
        ->with('te_linked')
        ->with('states')
        // ->with(['branch', 'branch.zone'])
        ->whereHas('branch', function($q){
            $q->whereIn('branch_id', $this->filteredBranch)->select([
                "branch.id AS branch_id",
                "branch.zone_id",
                "branch.name",
                "branch.branch_code",
                "branch.state_id",
                "branch.description",
                "branch.status",
                "branch.created_at",
                "branch.updated_at",
            ]);
        })->whereHas('branch.zone', function($q){
            $q->whereIn('id', $this->filteredZone);
        })
        ->with('by_created')
        ->where('role', 2)
        ->where('parent', $this->parent_id)
        ->whereIn('status', $this->isActive)
        ->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))
        ->orderBy('id', 'DESC')
        ->select(["users.*"]);
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
            'dom'       => 'Bfrtip',
            'stateSave' => true,
            'order'     => [[0, 'desc']],
            'buttons'   => ['excel'],
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
           
            Column::make('id')->searchable(false)->visible(false)->orderable(false),
            Column::make('name')->orderable(false),
            Column::make('profile_pic')->title('Image')->exportable(false)->searchable(false)->orderable(false),
            Column::make('profile')->title('Image')->visible(false)->searchable(false)->orderable(false),
            Column::make('address1')->orderable(false),
            Column::make('address2')->orderable(false),
            Column::make('city')->orderable(false),
            Column::make('district')->orderable(false),
            Column::make('state')->title('State')->orderable(false),
            Column::make('country')->orderable(false),
            Column::make('pincode')->orderable(false),
            Column::make('aadhaar_no')->orderable(false),
            Column::make('aadhaar_doc')->exportable(false)->searchable(false)->orderable(false),
            Column::make('aadhaar_doc_image')->title('aadhaar_doc')->visible(false)->searchable(false)->orderable(false),
            Column::make('dob')->orderable(false),
            Column::make('phone')->title('Phone Number')->orderable(false),
            Column::make('marital_status')->orderable(false),
            Column::make('spouse_name')->orderable(false),
            Column::make('spouse_dob')->orderable(false),
            Column::make('branch.name')->orderable(false),
            Column::make('branch.zone.name')->title('Zone')->orderable(false),
            Column::make('by_created.name')->title('Created By')->orderable(false),
            Column::make('te_linked.name')->title('Linked TE')->orderable(false),
            Column::make('te_linked.profile_pic')->title('TE Image')->exportable(false)->searchable(false)->orderable(false),
            Column::make('te_linked.image')->title('TE Image')->visible(false)->searchable(false)->orderable(false),
            // Column::make('te_linked.address')->title('TE Address'),
            Column::make('mason_dealers')->title('Linked Dealer')->visible(false)->orderable(false),
            Column::make('mason_dealers_code')->title('Dealer Code')->visible(false)->orderable(false),
            Column::make('points')->orderable(false),
            Column::make('status')->title("Mason Status")->orderable(false),
            Column::make('login_status')->orderable(false),
            Column::make('login_device_type')->title('Device Type')->orderable(false),
            Column::make('login_device_name')->title('Device Name')->orderable(false),
            Column::make('app_version')->title('App Version')->orderable(false),
            Column::make('dealer_linked_code')->title('Linked Dealer Code')->orderable(false),
            Column::make('dealer_linked_name')->title('Linked Dealer Name')->orderable(false),
            Column::make('created_at')->orderable(false),
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
