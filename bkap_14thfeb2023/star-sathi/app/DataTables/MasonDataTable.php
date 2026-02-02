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
        ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Mason $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->with('branch')->with('by_created')->where('role', 2)->orderBy('id', 'DESC');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('mason-table')
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
           
            Column::make('id')->searchable(false)->visible(false),
            Column::make('name'),
            Column::make('aadhaar_no'),
            Column::make('dob'),
            Column::make('spouse_name'),
            Column::make('spouse_dob'),
            Column::make('branch.name'),
            Column::make('by_created.name')->title('Created By'),
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
