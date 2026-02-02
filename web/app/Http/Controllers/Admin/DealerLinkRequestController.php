<?php

namespace App\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DealerLinkageRequest;
use App\Models\DealerLinkageRequestHistory;
use App\DataTables\DealerLinkRequestDataTable;

class DealerLinkRequestController extends Controller
{
    use HelperTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DealerLinkRequestDataTable $dataTable, Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealer_link_requests.view') ;
        return $dataTable->render('admin.dealer_link_requests.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealer_link_requests.create') ;
        return redirect(route("admin.dealer_link_requests.index"));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealer_link_requests.create') ;
        return redirect(route("admin.dealer_link_requests.index"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealer_link_requests.view') ;
        return redirect(route("admin.dealer_link_requests.index"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealer_link_requests.edit') ;
        return redirect(route("admin.dealer_link_requests.index"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealer_link_requests.edit') ;
        return redirect(route("admin.dealer_link_requests.index"));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dealer_link_requests.delete') ;
        return redirect(route("admin.dealer_link_requests.index"));
    }
    public function export()
    {
        $numberOfRecords = DealerLinkageRequest::count();
        set_time_limit(0);
        $filename = "Dealer_Linkage_Request_".$this->getUniqueId().".csv";
        $headings = [
            "Contractor Name",
            "Contractor Phone",
            "Contractor Branch",
            "Contractor Zone",
            "Approval BDE Name",
            "Approval BDE Phone",
            "Requested Dealer Name",
            "Requested Dealer Sap Code",
            "Request Send",
            "Status"
        ];

        $myfile = fopen(public_path("/excel_exports/dealer_link_requests/").$filename, "w");
        fputcsv($myfile,$headings);
        $fetchDataLimit = 1000;
        $fetchDataFrom = 0;
        $i = 0;
        while($i < $numberOfRecords)
        {
            $data = DealerLinkageRequest::with(['mason', 'mason.te_linked', 'mason.branch', 'mason.branch.zone', 'dealer', 'dealer_linkage_request_history'])->orderBy("id", "DESC")->skip($fetchDataFrom)->take($fetchDataLimit)->get();
            foreach($data as $val)
            {
                if($val->status == 0)
                    $status = 'Pending';
                else if($val->status == 1)
                    $status = 'Accepted';
                else if($val->status == 2)
                    $status = 'Rejected';
                else
                    $status = "";
                $requestSend = DealerLinkageRequestHistory::where([
                        'dealer_linkage_request_id' => $val->id,
                        'status' => 0
                    ])->orderBy('id', 'DESC')->pluck('created_at')->first()->toDateTimeString() ?? "";
                $content = [
                    $val->mason->name ?? "",
                    $val->mason->phone ?? "",
                    $val->mason->branch->name ?? "",
                    $val->mason->branch->zone->name ?? "",
                    $val->mason->te_linked->name ?? "",
                    $val->mason->te_linked->phone ?? "",
                    $val->dealer->name ?? "",
                    $val->dealer->sap_code ?? "",
                    $requestSend,
                    $status,
                ];
                fputcsv($myfile,$content);
            }
            $fetchDataFrom += $fetchDataLimit;
            $i += $fetchDataLimit;
        }
        fclose($myfile);
        $filePath = public_path("/excel_exports/dealer_link_requests/".$filename);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
