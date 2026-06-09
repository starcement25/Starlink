<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class DealerDataTable extends DataTable
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
        ->editColumn('branch.name', function (User $user) {
            return $user->branch->name ?? "" ;
        })
        ->editColumn('dealer_linked.name', function (User $user) {
            return $user->dealer_linked->name ?? "" ;
        })
        ->editColumn('dealer_linked.emp_code', function (User $user) {
            return $user->dealer_linked->emp_code ?? "" ;
        })
        ->editColumn('roles.role_name', function (User $user) {
            return $user->roles->role_name ?? "";
        })
        ->editColumn('status', function ($user) {
            $class = $user->status == 1 ? 'badge-success' : 'badge-danger' ;
            $text  = $user->status == 1 ? 'Active' : 'Disabled' ;
        
            return '<span class="badge '.$class.'">'.$text.'</span>';
        })
        ->addColumn('action', function ($user) {
            return '<form method="POST" action="'.route('dealers.destroy', ['dealer'=> $user->id]).'">
                       '.csrf_field().'
                        <input name="_method" type="hidden" value="DELETE">
                        <div class="btn-group">
                        <a href="'.route('dealers.edit', ['dealer'=> $user->id]).'" class="btn btn-default btn-xs">
                            <i class="far fa-edit"></i>
                        </a>
                       <!-- <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure?\')">
                            <i class="far fa-trash-alt"></i>
                        </button> -->
                    </div>';
         })
         ->setRowId('id')
         ->rawColumns(['action', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Dealer $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
    {
        $loggedUser=Auth::user();
        if($loggedUser->role > 6)
        {
            $allocated_branches=json_decode($loggedUser->allocated_branches);
            return $model->newQuery()->with('roles')->with('branch')->with('dealer_linked')->whereIn('role', ['3', '4', '6'])->whereIn('branch_id',$allocated_branches)->orderBy('id', 'DESC')
            ->select(["users.*"]);
        }
        else
        {
            return $model->newQuery()->with('roles')->with('branch')->with('dealer_linked')->where('status', 1)->whereIn('role', ['3', '4', '6'])->orderBy('id', 'DESC')
            ->select(["users.*"]);
        }
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('dealer-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
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
            
            Column::make('id')->visible(false)->searchable(false),
            Column::make('emp_code')->title('Customer Code'),
            Column::make('sap_code'),
            Column::make('name'),
            Column::make('roles.role_name')->title('Type'),
            Column::make('dealer_linked.emp_code')->title('Linked Dealer Code'),
            Column::make('dealer_linked.name')->title('Linked Dealer Name'),
            Column::make('branch.name')->title('Branch'),
            Column::make('phone'),
            Column::make('whatsapp_no')->title('WA No'),
            Column::make('status'),
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
        return 'Dealer_' . date('YmdHis');
    }
}
