<?php

namespace App\Http\Controllers\admin;

use App\Models\Zone;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\DataTables\DashboardDataTable;
use App\DataTables\MasonDashboardDataTable;
use App\DataTables\LiftingDashboardDataTable;
use App\DataTables\SupportDashboardDataTable;
use App\DataTables\MasonPointDashboardDataTable;
use App\DataTables\RedeemtionDashboardDataTable;

class DashboardController extends Controller
{
    public function dashboard(Request $request, DashboardDataTable $dataTable)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dashboard.view') ;
        $loggedUser=Auth::user();
        $from_date = $request->fromDate === null ? '1990-12-01' : $request->fromDate;
        $fromDataVal = $request->fromDate === null ? '' : $request->fromDate;
        $to_date = $request->toDate === null ? date('Y-m-d') : $request->toDate;
        $toDataVal = $request->toDate === null ? '' : $request->toDate;
        if($loggedUser->role > 6)
        {
            $allocated_branches=json_decode($loggedUser->allocated_branches);
            $branches = Branch::where('status',1)->whereIn('id', $allocated_branches)->get();
            $zones = Zone::where('status',1)->whereHas('branches', function($q) use($allocated_branches){
                $q->where('status',1)->whereIn('id', $allocated_branches);
            })->get();
        }
        else
        {
            $branches = Branch::where('status',1)->get();
            $zones = Zone::where('status',1)->get();
        }
        $filteredZone = $request->zone === null ? $zones->pluck('id')->toArray() : [$request->zone];
        $zoneSelect = $request->zone === null ? '' : $request->zone;
        $filteredBranch = $request->branch === null ? $branches->pluck('id')->toArray() : [$request->branch];
        $branchSelect = $request->branch === null ? '' : $request->branch;
        return $dataTable->with([
            'from_date' => $from_date,
            'to_date' => $to_date,
            'filteredBranch' => $filteredBranch,
            'filteredZone' => $filteredZone,
        ])->render('admin.dashboard.index', [
            'fromDataVal'=>$fromDataVal,
            'toDataVal'=>$toDataVal,
            'zones'=>$zones,
            'zoneSelect' => $zoneSelect,
            'branches' => $branches,
            'branchSelect' => $branchSelect,
        ]);
    }
    public function masonDashboard(Request $request, MasonDashboardDataTable $dataTable)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dashboard.view') ;
        $isActive = $request->has('isActive') ? $request->isActive : [0,1];
        $from_date = $request->fromDate === null ? '1990-12-01' : $request->fromDate;
        $to_date = $request->toDate === null ? date('Y-m-d') : $request->toDate;
        return $dataTable->with([
            'from_date' => $from_date,
            'to_date' => $to_date,
            'filteredBranch' => $request->filteredBranch,
            'filteredZone' => $request->filteredZone,
            'parent_id' => $request->parent_id,
            'isActive' => $isActive,
        ])->render('admin.dashboard.mason-dashboard.index');
    }
    public function masonPointDashboard(Request $request, MasonPointDashboardDataTable $dataTable)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dashboard.view') ;
        $from_date = $request->fromDate === null ? '1990-12-01' : $request->fromDate;
        $to_date = $request->toDate === null ? date('Y-m-d') : $request->toDate;
        return $dataTable->with([
            'from_date' => $from_date,
            'to_date' => $to_date,
            'filteredBranch' => $request->filteredBranch,
            'filteredZone' => $request->filteredZone,
            'parent_id' => $request->parent_id,
        ])->render('admin.dashboard.mason-dashboard.point');
    }
    public function liftingDashboard(Request $request, LiftingDashboardDataTable $dataTable)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dashboard.view') ;
        $from_date = $request->fromDate === null ? '1990-12-01' : $request->fromDate;
        $to_date = $request->toDate === null ? date('Y-m-d') : $request->toDate;
        return $dataTable->with([
            'from_date' => $from_date,
            'to_date' => $to_date,
            'filteredBranch' => $request->filteredBranch,
            'filteredZone' => $request->filteredZone,
            'parent_id' => $request->parent_id,
            'lifting' => $request->lifting,
            'product' => $request->product
        ])->render('admin.dashboard.lifting-dashboard.index',[
            'lifting' => $request->lifting,
            'product' => $request->product
        ]);
    }
    public function redeemtionDashboard(Request $request, RedeemtionDashboardDataTable $dataTable)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dashboard.view') ;
        $from_date = $request->fromDate === null ? '1990-12-01' : $request->fromDate;
        $to_date = $request->toDate === null ? date('Y-m-d') : $request->toDate;
        return $dataTable->with([
            'from_date' => $from_date,
            'to_date' => $to_date,
            'filteredBranch' => $request->filteredBranch,
            'filteredZone' => $request->filteredZone,
            'parent_id' => $request->parent_id,
            'status' => $request->status
        ])->render('admin.dashboard.redeemtion-dashboard.index',[
            'status' => $request->status
        ]);
    }
    public function supportDashboard(Request $request, SupportDashboardDataTable $dataTable)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('dashboard.view') ;
        $from_date = $request->fromDate === null ? '1990-12-01' : $request->fromDate;
        $to_date = $request->toDate === null ? date('Y-m-d') : $request->toDate;
        return $dataTable->with([
            'from_date' => $from_date,
            'to_date' => $to_date,
            'filteredBranch' => $request->filteredBranch,
            'filteredZone' => $request->filteredZone,
            'parent_id' => $request->parent_id,
            'status' => $request->status
        ])->render('admin.dashboard.support-dashboard.index',[
            'status' => $request->status
        ]);
    }
}
