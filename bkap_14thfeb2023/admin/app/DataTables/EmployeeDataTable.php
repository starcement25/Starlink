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
         	->editColumn('branch.name', function ($user) {
                        return $user->branch->name ?? "";
              })
        	->editColumn('branch.zone.name', function ($user) {
                        return $user->branch->zone->name ?? "";
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
            ->rawColumns(['action', 'status'])
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
        return $model->newQuery()->with(['branch', 'branch.zone'])->where('role', 1)->orderBy('id', 'DESC');
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
                    //->dom('Bfrtip')
                    ->orderBy(1)
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
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
            Column::make('designation'),
            Column::make('name'),
            Column::make('phone'),
            Column::make('email'),
            Column::make('branch.name')->title('Branch'),
            Column::make('branch.zone.name')->title('Zone'),
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
}
