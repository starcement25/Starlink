<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MasonPointDashboardDataTable extends DataTable
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
            ->editColumn('branch.name', function ($user) {
                return $user->branch->name ?? "";
            })
            ->editColumn('te_linked.emp_code',function($user){
                return $user->te_linked->emp_code ?? "";
            })
            ->editColumn('te_linked.name',function($user){
                return $user->te_linked->name ?? "";
            })
            ->addColumn('mason_category.name',function($user){
                return $user->mason_category->name ?? "";
            })
            ->editColumn('branch.zone.name', function ($user) {
                return $user->branch->zone->name ?? "";
            })
            ->editColumn('te_linked.phone',function($user){
                return $user->te_linked->phone ?? "";
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Mason $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
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
        ->with('te_linked')
        ->whereBetween(DB::raw('DATE(created_at)'), array($this->from_date, $this->to_date))
        ->where('role', 2)
        ->where('parent', $this->parent_id)
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
           
            Column::make('id')->searchable(false)->visible(false)->exportable(false),
            Column::make('name'),
            Column::make('phone')->title('Contact'),
            Column::make('points'),
            Column::make('mason_category.name')->title('Mason Category'),
            Column::make('branch.name')->searchable(false),
            Column::make('branch.zone.name')->searchable(false)->title('Zone'),
            Column::make('te_linked.emp_code')->title('TE Code'),
            Column::make('te_linked.name')->title('TE Name'),
            Column::make('te_linked.phone')->title('TE Mobile'),
            
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Mason_Points_' . date('YmdHis');
    }
}
