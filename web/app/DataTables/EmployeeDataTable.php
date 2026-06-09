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

class EmployeeDataTable extends DataTable
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
            ->editColumn('status', function ($user) {
                $class = $user->status == 1 ? 'badge-success' : 'badge-danger' ;
                $text  = $user->status == 1 ? 'Active' : 'Disabled' ;
            
                return '<span class="badge '.$class.'">'.$text.'</span>';
            })
            ->editColumn('employee_branch.name', function (User $user) {
                $data = $user->employee_branch?->pluck('name')?->toArray() ?? "" ;
                if($data != "")
                {
                    return  implode(",",$data);
                }
                return $data;
               
              
            })
            ->editColumn('employee_branch.zone', function (User $user) {
                //     $datas = $user->employee_branch?->all() ;
                //     $temp= [] ;
                //     foreach($datas as $data){
                //        $temp[] = $data['zone']['name'];
                //     }
                //  return  implode(",",$temp);

                return $user->employee_branch
                            ?->pluck('zone.name')
                            ->filter()
                            ->implode(',');
                  
              })
            ->editColumn('states.state_name', function (User $user) {
                $data = $user->states->state_name ?? "" ;
               return $data   ;
              
               })
           
            ->editColumn('action', function(User $user){
                return '<form method="POST" action="'.route('employees.destroy', ['employee'=> $user->id]).'">
                '.csrf_field().'
                 <input name="_method" type="hidden" value="DELETE">
                 <div class="btn-group">
                 <a href="'.route('employees.edit', ['employee'=> $user->id]).'" class="btn btn-default btn-xs">
                     <i class="far fa-edit"></i>
                 </a>
                <!-- <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure?\')">
                     <i class="far fa-trash-alt"></i>
                 </button>-->
             </div>';
            })
            ->editColumn('profile',function(User $user){
                return $user->profile_pic == Null ? "" : "<img src='".$user->profile_pic."' height=100 width=100>";
            })
            ->editColumn('profile_pic',function(User $user){
                return $user->profile_pic == Null ? "" : "<a href='".$user->profile_pic."'>".$user->profile_pic."</a>";
            })
            ->rawColumns(['action', 'status','profile','profile_pic'])
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Employee $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
    {
        $loggedUser=Auth::user();
        if($loggedUser->role > 6)
        {
            $allocated_branches=json_decode($loggedUser->allocated_branches);
            return $model->newQuery()->with('states')->with(['employee_branch', 'employee_branch.zone'])->where('role', 1)->whereHas('employee_branch',function($q) use($allocated_branches){
                $q->whereIn('branch_id',$allocated_branches);
            })->orderBy('id', 'DESC')->select(["users.*"]);
        }
        else
        {
          
            return $model->newQuery()->with('states')->with(['employee_branch', 'employee_branch.zone'])->where('role', 1)->orderBy('id', 'DESC')->select(["users.*"]);
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
                    ->setTableId('employee-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->parameters([
                        'dom'       => 'Bfrtip',
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
            Column::make('emp_code'),
            Column::make('profile')->title('profile')->exportable(false)->searchable(false),
            Column::make('profile_pic')->title('image')->visible(false)->searchable(false),
            Column::make('designation'),
            Column::make('name'),
            // Column::make('address1'),
            // Column::make('address2'),
            // Column::make('city'),
            // Column::make('district'),
            // Column::make('pincode'),
            // Column::make('states.state_name')->title('State'),
            Column::make('phone'),
            Column::make('email'),
            Column::make('employee_branch.name')->title('Branch'),
            Column::make('employee_branch.zone')->title('Zone')->searchable(false),
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
        return 'Employee_' . date('YmdHis');
    }
    public function array_column_multi ($array, $column) {
        $types = array_unique(array_column($array, $column));
    
        $return = [];
        foreach ($types as $type) {
            foreach ($array as $key => $value) {
                if ($type === $value[$column]) {
                    unset($value[$column]);
                    $return[$type][] = $value;
                    unset($array[$key]);
                }
            }
        }
        return $return;
    }
}
