<?php

namespace App\DataTables;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BranchDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $dataTable = new EloquentDataTable($query);
       
        $dataTable = $dataTable
                    ->editColumn('status', function ($branch) {
                        $class = $branch->status == 1 ? 'badge-success' : 'badge-danger' ;
                        $text  = $branch->status == 1 ? 'Active' : 'Disabled' ;
                       
                        return '<span class="badge '.$class.'">'.$text.'</span>';
                    })
                    ->editColumn('state.state_name', function ($branch) {
                        return $branch->state->state_name ?? "";
                    })
                    ->addColumn('action', function ($branch) {
                        return '<form method="POST" action="'.route('branch.destroy', ['branch'=> $branch->id]).'">
                                   '.csrf_field().'
                                    <input name="_method" type="hidden" value="DELETE">
                                    <div class="btn-group">
                                    <a href="'.route('branch.edit', ['branch'=> $branch->id]).'" class="btn btn-default btn-xs">
                                        <i class="far fa-edit"></i>
                                    </a>
                                   <!-- <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure?\')">
                                        <i class="far fa-trash-alt"></i>
                                    </button>-->
                                </div>';
                     })
                    ->rawColumns(['action', 'status']);
    
        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Branch $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Branch $model): QueryBuilder
    {
        return $model->newQuery()->with('zone')->with('state')->orderBy('id', 'DESC');
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
            'buttons'   => ['excel', 'csv'
                // Enable Buttons as per your need
//                    ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner',],
//                    ['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner',],
//                    ['extend' => 'print', 'className' => 'btn btn-default btn-sm no-corner',],
//                    ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner',],
//                    ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner',],
            ],
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
           
            Column::make('id'),
            Column::make('name'),
            Column::make('branch_code'),
            Column::make('zone.name'),
            Column::make('state.state_name')->title('State'),
            Column::make('description'),
            Column::make('status'),
            Column::computed('action')
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
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
        return 'Branch_' . date('YmdHis');
    }
}
