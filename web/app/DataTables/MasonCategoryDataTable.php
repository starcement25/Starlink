<?php

namespace App\DataTables;

use App\Models\MasonCategory;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class MasonCategoryDataTable extends DataTable
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
                    ->addColumn('action', function ($category) {
                        return '<form method="POST" action="'.route('mason-categories.destroy', ['mason_category'=> $category->id]).'">
                                   '.csrf_field().'
                                    <input name="_method" type="hidden" value="DELETE">
                                    <div class="btn-group">
                                    <a href="'.route('mason-categories.edit', ['mason_category'=> $category->id]).'" class="btn btn-default btn-xs">
                                        <i class="far fa-edit"></i>
                                    </a>
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure?\')">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </div>';
                     })
                    ->rawColumns(['action', 'image']);
    
        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\MasonCategory $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(MasonCategory $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {

        return $this->builder()
                    ->setTableId('mason-category-table')
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
            Column::make('id')->title('id')->searchable(false),
            Column::make('name'),
            Column::make('from_point'),
            Column::make('to_point'),
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
        return 'MasonCategory_' . date('YmdHis');
    }
}
