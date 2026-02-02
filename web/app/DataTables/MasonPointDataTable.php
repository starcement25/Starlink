<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;

class MasonPointDataTable extends DataTable
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
            ->editColumn('by_created.name', function(User $user){
               return $user->by_created->name ?? "" ;
            })
            ->editColumn('by_created.emp_code', function(User $user){
               return $user->by_created->emp_code ?? "" ;
            })
            ->editColumn('branch.name', function ($user) {
                return $user->branch->name ?? "";
            })
            ->addColumn('action', function(User $user){
                return ' <div class="btn-group">
                            <a href="'.route('point.manupulate', ['user'=> $user->id]).'" class="btn btn-default btn-xs">
                                <i class="far fa-edit"></i>
                            </a>
                        </div>' ;
            })
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\MasonPoint $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
    {
        $loggedUser=Auth::user();
        if($loggedUser->id > 6)
        {
            $allocated_branches=json_decode($loggedUser->allocated_branches);
            return $model->newQuery()->with('by_created')->with('branch')->where('role', '2')->whereIn('branch_id',$allocated_branches)->orderBy('id', 'DESC');
        }
        else
        {
            return $model->newQuery()->with('by_created')->with('branch')->where('role', '2')->orderBy('id', 'DESC');
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
                    ->setTableId('masonpoint-table')
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
            Column::make('name'),
            Column::make('phone'),
            Column::make('by_created.name')->title('Employee Name'),
            Column::make('by_created.emp_code')->title('Employee Id'),
            Column::make('branch.name'),
            Column::make('points'),
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
        return 'MasonPoint_' . date('YmdHis');
    }
}
