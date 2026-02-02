<?php

namespace App\DataTables;

use App\Models\DealerLinkageRequest;
use App\Models\DealerLinkageRequestHistory;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DealerLinkRequestDataTable extends DataTable
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
        ->editColumn('mason.name', function ($dealerLinking) {
            return $dealerLinking->mason->name ?? "";
        })
        ->editColumn('mason.phone', function ($dealerLinking) {
            return $dealerLinking->mason->phone ?? "";
        })
        ->editColumn('mason.branch.name', function ($dealerLinking) {
            return $dealerLinking->mason->branch->name ?? "";
        })
        ->editColumn('mason.branch.zone.name', function ($dealerLinking) {
            return $dealerLinking->mason->branch->zone->name ?? "";
        })
        ->editColumn('mason.te_linked.name', function ($dealerLinking) {
            return $dealerLinking->mason->te_linked->name ?? "";
        })
        ->editColumn('mason.te_linked.phone', function ($dealerLinking) {
            return $dealerLinking->mason->te_linked->phone ?? "";
        })
        ->editColumn('dealer.name', function ($dealerLinking) {
            return $dealerLinking->dealer->name ?? "";
        })
        ->editColumn('dealer.sap_code', function ($dealerLinking) {
            return $dealerLinking->dealer->sap_code ?? "";
        })
        ->editColumn('dealer_linkage_request_history.created_at', function ($dealerLinking) {
            return DealerLinkageRequestHistory::where([
                'dealer_linkage_request_id' => $dealerLinking->id,
                'status' => 0
            ])->orderBy('id', 'DESC')->pluck('created_at')->first()->toDateTimeString() ?? "";
        })
        ->editColumn('status', function ($dealerLinking) {
            if($dealerLinking->status == 0)
                $status = '<span class="badge badge-primary">Pending</span>';
            else if($dealerLinking->status == 1)
                $status = '<span class="badge badge-success">Accepted</span>';
            else if($dealerLinking->status == 2)
                $status = '<span class="badge badge-danger">Rejected</span>';
            else
                $status = "";
            return $status;
        })
        ->rawColumns(['status']);


    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\DealerLinkageRequest $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DealerLinkageRequest $model): QueryBuilder
    {
        return $model->newQuery()->with(['mason', 'mason.te_linked', 'mason.branch', 'mason.branch.zone', 'dealer', 'dealer_linkage_request_history'])->orderBy("id", "DESC");
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('dealerlinkrequests-table')
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

            Column::make('mason.name')->title("Contractor Name")->orderable(false),
            Column::make('mason.phone')->title("Contractor Phone")->orderable(false),
            Column::make('mason.branch.name')->title("Contractor Branch")->orderable(false),
            Column::make('mason.branch.zone.name')->title("Contractor Zone")->orderable(false),
            Column::make('mason.te_linked.name')->title("Approval BDE Name")->orderable(false),
            Column::make('mason.te_linked.phone')->title("Approval BDE Phone")->orderable(false),
            Column::make('dealer.name')->title("Requested Dealer Name")->orderable(false),
            Column::make('dealer.sap_code')->title("Requested Dealer Sap Code")->orderable(false),
            Column::make('dealer_linkage_request_history.created_at')->title("Request Send")->orderable(false),
            Column::make('status')->orderable(false),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'DealerLinkRequests_' . date('YmdHis');
    }
}
