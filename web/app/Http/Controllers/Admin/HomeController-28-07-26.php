<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Catalogue;
use App\Models\UserCatalogueRedeemtion;
use App\Models\Zone;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function home(Request $request)
    {
       // return "hi";
        // ----------------------------------------
        // 1. MASON SUMMARY FILTERS & DATA
        // ----------------------------------------
        $monthMap = [
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4,
            'may' => 5, 'jun' => 6, 'jul' => 7, 'aug' => 8,
            'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12
        ];

        $masonMonth = $request->input('mason_month', 'current');
        $masonMonthNum = $masonMonth === 'current'
            ? Carbon::now()->month
            : ($monthMap[strtolower($masonMonth)] ?? Carbon::now()->month);
        $currentYear = Carbon::now()->year;
        $masonYear = (int) $request->input('mason_year', $currentYear);
        if ($masonYear < 2000 || $masonYear > $currentYear + 1) {
            $masonYear = $currentYear;
        }
        $masonYears = range($currentYear, $currentYear - 4);

        $startDate = Carbon::create($masonYear, $masonMonthNum, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $prevStartDate = $startDate->copy()->subMonth()->startOfMonth();
        $prevEndDate = $startDate->copy()->subMonth()->endOfMonth();

        // Total Active Masons
        $totalMasons = User::where('role', 2)->where('status', 1)->count();

        // Total Inactive Masons
        $totalInactiveMasons = User::where('role', 2)->where('status', 0)->count();

        // Zone-wise Masons
        $zones = ['NE1', 'NE2', 'NB1', 'NB2', 'BIHAR'];
        $zoneMasonsTotal = [];
        $zoneMasonsThisMonth = [];
        $zoneMasonsLastMonth = [];

        foreach ($zones as $zoneName) {
            $zoneMasonsTotal[$zoneName] = User::where('users.role', 2)
                ->where('users.status', 1)
                ->join('branch', 'users.branch_id', '=', 'branch.id')
                ->join('zones', 'branch.zone_id', '=', 'zones.id')
                ->whereRaw('LOWER(zones.name) = ?', [strtolower($zoneName)])
                ->count();

            $zoneMasonsThisMonth[$zoneName] = User::where('users.role', 2)
                ->where('users.status', 1)
                ->join('branch', 'users.branch_id', '=', 'branch.id')
                ->join('zones', 'branch.zone_id', '=', 'zones.id')
                ->whereRaw('LOWER(zones.name) = ?', [strtolower($zoneName)])
                ->whereBetween('users.created_at', [$startDate, $endDate])
                ->count();

            $zoneMasonsLastMonth[$zoneName] = User::where('users.role', 2)
                ->where('users.status', 1)
                ->join('branch', 'users.branch_id', '=', 'branch.id')
                ->join('zones', 'branch.zone_id', '=', 'zones.id')
                ->whereRaw('LOWER(zones.name) = ?', [strtolower($zoneName)])
                ->whereBetween('users.created_at', [$prevStartDate, $prevEndDate])
                ->count();
        }

        // ----------------------------------------
        // 2. ORDER SUMMARY FILTERS & DATA
        // ----------------------------------------
        $orderCtx = $this->orderFilterContext($request);

        $orderMonth   = $orderCtx['month'];
        $orderZone    = $orderCtx['zone'];
        $orderFromVal = $orderCtx['fromVal'];
        $orderToVal   = $orderCtx['toVal'];

        $orderZones = Zone::where('status', 1)->orderBy('name', 'asc')->get();

        $applyFilters = function ($query) use ($orderCtx) {
            return $this->applyOrderFilters($query, $orderCtx);
        };

        // Redeemable Products = the active catalogue (product) list, not the redemptions.
        $redeemProductsCount = Catalogue::where('status', Catalogue::STATUS_ACTIVE)->count();

        // Query Builders for Orders metrics (fully qualified to prevent ambiguity during join)
        $totalOrdersQuery = UserCatalogueRedeemtion::query();
        $pendingOrdersQuery = UserCatalogueRedeemtion::where('user_catalogue_redeemtions.status', UserCatalogueRedeemtion::STATUS_PENDING);
        $placedOrdersQuery = UserCatalogueRedeemtion::where('user_catalogue_redeemtions.status', UserCatalogueRedeemtion::STATUS_ORDER_PLACED);
        $deliveredOrdersQuery = UserCatalogueRedeemtion::where('user_catalogue_redeemtions.status', UserCatalogueRedeemtion::STATUS_DELIVERED);
        $rejectedOrdersQuery = UserCatalogueRedeemtion::where('user_catalogue_redeemtions.status', UserCatalogueRedeemtion::STATUS_REJECTED);

        $totalOrders = $applyFilters($totalOrdersQuery)->count();
        $pendingOrders = $applyFilters($pendingOrdersQuery)->count();
        $placedOrders = $applyFilters($placedOrdersQuery)->count();
        $deliveredOrders = $applyFilters($deliveredOrdersQuery)->count();
        $rejectedOrders = $applyFilters($rejectedOrdersQuery)->count();

        // Orders still pending after 7 days
        $pendingOver7Days = $applyFilters($this->pendingOverSevenDaysQuery())->count();

        // Pending Remarks count
        $remarksList = [
            'Order confirmation not received',
            'Address confirmation not received',
            'Product unavailability & alternatives',
            'Customer not answering calls',
            'Incomplete delivery address',
            'Wrong mobile number'
        ];
        $remarksCounts = [];
        foreach ($remarksList as $remark) {
            $remarkQuery = UserCatalogueRedeemtion::where('user_catalogue_redeemtions.status', UserCatalogueRedeemtion::STATUS_PENDING)
                ->where('user_catalogue_redeemtions.remarks', $remark);
            $remarksCounts[$remark] = $applyFilters($remarkQuery)->count();
        }
//return $totalMasons;
        return view('admin.home', compact(
            'totalMasons',
            'totalInactiveMasons',
            'zoneMasonsTotal',
            'zoneMasonsThisMonth',
            'zoneMasonsLastMonth',
            'masonMonth',
            'masonYear',
            'masonYears',
            'redeemProductsCount',
            'totalOrders',
            'pendingOrders',
            'placedOrders',
            'deliveredOrders',
            'rejectedOrders',
            'pendingOver7Days',
            'remarksCounts',
            'orderMonth',
            'orderFromVal',
            'orderToVal',
            'orderZones',
            'orderZone'
        ));
    }

    /**
     * CSV of every order still sitting in "Pending" more than 7 days after it was
     * placed. Honours the same zone / month / date-range filters as the dashboard
     * panel, so the file always matches the number shown on the button's row.
     */
    public function exportPendingOrders(Request $request)
    {
        set_time_limit(0);

        $orderCtx = $this->orderFilterContext($request);

        $query = $this->pendingOverSevenDaysQuery()
            ->select('user_catalogue_redeemtions.*')
            ->with(['user.branch.zone', 'user.te_linked', 'catalogue']);

        $this->applyOrderFilters($query, $orderCtx);

        $headings = [
            'Order No',
            'Order Date',
            'Days Pending',
            'Contractor Name',
            'Contractor Phone',
            'BDE Code',
            'BDE Name',
            'Branch',
            'Zone',
            'Product',
            'Redeemed Point',
            'Remarks',
            'Delivery Address',
            'City',
            'District',
            'State',
            'Pincode',
        ];

        $filename = 'pending_orders_over_7_days_' . Carbon::now()->format('Y-m-d_His') . '.csv';
        $now = Carbon::now();

        return response()->streamDownload(function () use ($query, $headings, $now) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headings);

            $clean = function ($value) {
                return (is_null($value) || $value === 'null') ? '' : $value;
            };

            $query->orderBy('user_catalogue_redeemtions.id', 'DESC')
                ->chunk(500, function ($orders) use ($handle, $now, $clean) {
                    foreach ($orders as $order) {
                        $placedAt = $order->created_at ? Carbon::parse($order->created_at) : null;

                        fputcsv($handle, [
                            $order->order_id,
                            $placedAt ? $placedAt->format('d-m-Y') : '',
                            $placedAt ? (int) $placedAt->diffInDays($now) : '',
                            $order->user->name ?? '',
                            $order->user->phone ?? '',
                            $order->user->te_linked->emp_code ?? '',
                            $order->user->te_linked->name ?? '',
                            $order->user->branch->name ?? '',
                            $order->user->branch->zone->name ?? '',
                            $order->catalogue->name ?? '',
                            $order->redeemed_point,
                            $clean($order->remarks),
                            trim($clean($order->address1) . ' ' . $clean($order->address2)),
                            $clean($order->city),
                            $clean($order->district),
                            $clean($order->state),
                            $clean($order->pincode),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Base query for orders left pending for more than 7 days.
     */
    private function pendingOverSevenDaysQuery()
    {
        return UserCatalogueRedeemtion::where('user_catalogue_redeemtions.status', UserCatalogueRedeemtion::STATUS_PENDING)
            ->where('user_catalogue_redeemtions.created_at', '<', Carbon::now()->subDays(7));
    }

    /**
     * Resolve the Order Summary filters (zone + month or explicit date range) once,
     * so the dashboard and the export can never drift apart.
     */
    private function orderFilterContext(Request $request): array
    {
        $monthMap = [
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4,
            'may' => 5, 'jun' => 6, 'jul' => 7, 'aug' => 8,
            'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12
        ];

        $month = $request->input('order_month', 'current');
        $from  = $request->input('order_from');
        $to    = $request->input('order_to');

        $isFiltered = ($from && $to) || ($month && $month !== 'current');
        $startDate = null;
        $endDate = null;
        $fromVal = '';
        $toVal = '';

        if ($isFiltered) {
            if ($from && $to) {
                $startDate = Carbon::parse($from)->startOfDay();
                $endDate = Carbon::parse($to)->endOfDay();
            } else {
                $monthNum = $monthMap[strtolower($month)] ?? Carbon::now()->month;
                $startDate = Carbon::create(Carbon::now()->year, $monthNum, 1)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
            }
            $fromVal = $from ?: $startDate->format('Y-m-d');
            $toVal = $to ?: $endDate->format('Y-m-d');
        }

        return [
            'month'      => $month,
            'zone'       => $request->input('order_zone'),
            'isFiltered' => $isFiltered,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
            'fromVal'    => $fromVal,
            'toVal'      => $toVal,
        ];
    }

    /**
     * Apply the resolved zone / date filters to an orders query.
     */
    private function applyOrderFilters($query, array $ctx)
    {
        if ($ctx['zone']) {
            $query->join('users', 'user_catalogue_redeemtions.user_id', '=', 'users.id')
                  ->join('branch', 'users.branch_id', '=', 'branch.id')
                  ->join('zones', 'branch.zone_id', '=', 'zones.id')
                  ->where('zones.id', $ctx['zone']);
        }

        if ($ctx['isFiltered']) {
            $query->whereBetween('user_catalogue_redeemtions.created_at', [$ctx['startDate'], $ctx['endDate']]);
        }

        return $query;
    }
}
