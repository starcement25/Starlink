<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;

class MasonLedgerDataTable extends DataTable
{
    public function dataTable($query)
    {
        return DataTables::of($query)
            ->editColumn('ledger_date', fn($row) => $row->ledger_date ? date('d-m-Y H:i', strtotime($row->ledger_date)) : '')
            ->editColumn('mason_details', function ($row) {
                $md = @json_decode($row->mason_details);
                return $md ? ($md->name ?? '') . ($md->phone ? " ({$md->phone})" : '') : $row->mason_details;
            })
            ->addColumn('address', function ($row) {
            $parts = array_filter([
                $row->address1,
                $row->address2,
                $row->city,
                $row->district,
                $row->state,
                $row->pincode
            ]);

            return implode(', ', $parts);
        })
            ->editColumn('credit_point', fn($r) => (float) $r->credit_point)
            ->editColumn('debit_point', fn($r) => (float) $r->debit_point)
            ->editColumn('remaining_point', fn($r) => $r->remaining_point !== null ? (float)$r->remaining_point : "-");
    }

    public function query()
    {
        $loggedUser = Auth::user();
        $user = $this->user;

        // Determine allowed user IDs
        if ($user === "ALL" && $loggedUser->role > 6) {
            $allocated = json_decode($loggedUser->allocated_branches);
            $userIds = User::whereIn('branch_id', $allocated)->pluck('id')->toArray();
        } elseif ($user === "ALL") {
            $userIds = User::pluck('id')->toArray();
        } else {
            $userIds = [$user];
        }

        // Prevent empty IN ()
        
        //$userIds =  [34646, 52949] ;
       // dd($userIds);

        $idList = implode(",", $userIds);

        if (empty($idList)) {
            return DB::query()->from(DB::raw("(SELECT * FROM (SELECT 1 AS dummy) AS x WHERE 1=0) AS ledger"));
        }

        // Date filters
        $fromDate = request()->get('date_from') ? request()->get('date_from') . " 00:00:00" : "1970-01-01 00:00:00";
        $toDate   = request()->get('date_to')   ? request()->get('date_to') . " 23:59:59" : now()->endOfDay()->format("Y-m-d H:i:s");

        // If ALL users -> NO running balance, NO opening balance
        if ($user === "ALL") {
            return $this->queryAllUsers($idList, $fromDate, $toDate);
        }

        // Single user -> FULL ledger with running balance
        return $this->querySingleUser($idList, $fromDate, $toDate);
    }


    // --------------------------
    // QUERY FOR ALL USERS
    // --------------------------
    private function queryAllUsers($idList, $fromDate, $toDate)
    {
        // $sql = "
        // SELECT 
        //     id,
        //     get_mason_details(user_id) AS mason_details,
        //     order_id,
        //     user_id,
        //     credit_point,
        //     debit_point,
        //     description,
        //     created_at,
        //     ledger_date,
        //     lifting_date,
        //     NULL AS remaining_point
        // FROM (

        //     SELECT 
        //         ucr.id,
        //         ucr.order_id,
        //         ucr.user_id,
        //         0 AS credit_point,
        //         ucr.redeemed_point AS debit_point,
        //         ucr.description,
        //         ucr.created_at,
        //         ucr.created_at AS ledger_date,
        //         NULL AS lifting_date
        //     FROM user_catalogue_redeemtions ucr
        //     WHERE ucr.user_id IN ($idList)
        //     AND ucr.created_at BETWEEN '$fromDate' AND '$toDate'

        //     UNION ALL

        //     SELECT 
        //         r.id,
        //         NULL AS order_id,
        //         r.user_id,
        //         CASE WHEN r.is_verified = 1 THEN r.point ELSE 0 END AS credit_point,
        //         CASE WHEN r.is_verified != 1 THEN r.point ELSE 0 END AS debit_point,
        //         r.description,
        //         r.created_at,
        //         r.updated_at AS ledger_date,
        //         l.lifting_date
        //     FROM rewards r
        //     LEFT JOIN lifting l ON r.lifting_id = l.id
        //     WHERE r.user_id IN ($idList)
        //     AND r.is_eligible_for_ledger = 1
        //     AND r.updated_at BETWEEN '$fromDate' AND '$toDate'

        //     UNION ALL

        //     SELECT 
        //         rh.id,
        //         NULL AS order_id,
        //         rh.user_id,
        //         CASE WHEN rh.is_verified = 1 THEN rh.point ELSE 0 END AS credit_point,
        //         CASE WHEN rh.is_verified != 1 THEN rh.point ELSE 0 END AS debit_point,
        //         rh.description,
        //         rh.created_at,
        //         rh.reward_date_time AS ledger_date,
        //         l.lifting_date
        //     FROM reward_history rh
        //     LEFT JOIN lifting l ON rh.lifting_id = l.id
        //     WHERE rh.user_id IN ($idList)
        //     AND rh.is_eligible_for_ledger = 1
        //     AND rh.reward_date_time BETWEEN '$fromDate' AND '$toDate'

        //     UNION ALL

        //     SELECT 
        //         rr.id,
        //         NULL AS order_id,
        //         rr.user_id,
        //         rr.point_credited AS credit_point,
        //         0 AS debit_point,
        //         rr.description,
        //         rr.created_at,
        //         rr.created_at AS ledger_date,
        //         NULL AS lifting_date
        //     FROM rejected_redeemtions rr
        //     WHERE rr.user_id IN ($idList)
        //     AND rr.created_at BETWEEN '$fromDate' AND '$toDate'

        // ) AS x
        // ORDER BY user_id, ledger_date ASC
        // ";

        $sql = "
            SELECT 
                id,
                get_mason_details(user_id) AS mason_details,
                order_id,
                user_id,

                credit_point,
                catalogue_point,
                tds_point,
                debit_point,

                description,

                address1,
                address2,
                city,
                district,
                state,
                pincode,
                phone,

                created_at,
                ledger_date,
                lifting_date,
                NULL AS remaining_point
            FROM (

                /* ---------- 1. Catalogue Redeem ---------- */
                SELECT 
                    ucr.id,
                    ucr.order_id,
                    ucr.user_id,

                    0 AS credit_point,
                    ucr.catalogue_point AS catalogue_point,
                    ucr.catalogue_tds_point AS tds_point,
                    ucr.redeemed_point AS debit_point,

                    ucr.description,

                    ucr.address1,
                    ucr.address2,
                    ucr.city,
                    ucr.district,
                    ucr.state,
                    ucr.pincode,
                    ucr.phone,

                    ucr.created_at,
                    ucr.created_at AS ledger_date,
                    NULL AS lifting_date
                FROM user_catalogue_redeemtions ucr
                WHERE ucr.user_id IN ($idList)
                AND ucr.created_at BETWEEN '$fromDate' AND '$toDate'

                UNION ALL

                /* ---------- 2. Rewards ---------- */
                SELECT 
                    r.id,
                    NULL AS order_id,
                    r.user_id,

                    CASE WHEN r.is_verified = 1 THEN r.point ELSE 0 END AS credit_point,
                    0 AS catalogue_point,
                    0 AS tds_point,
                    CASE WHEN r.is_verified != 1 THEN r.point ELSE 0 END AS debit_point,

                    r.description,

                    NULL AS address1,
                    NULL AS address2,
                    NULL AS city,
                    NULL AS district,
                    NULL AS state,
                    NULL AS pincode,
                    NULL AS phone,

                    r.created_at,
                    r.updated_at AS ledger_date,
                    l.lifting_date
                FROM rewards r
                LEFT JOIN lifting l ON r.lifting_id = l.id
                WHERE r.user_id IN ($idList)
                AND r.is_eligible_for_ledger = 1
                AND r.updated_at BETWEEN '$fromDate' AND '$toDate'

                UNION ALL

                /* ---------- 3. Reward History ---------- */
                SELECT 
                    rh.id,
                    NULL AS order_id,
                    rh.user_id,

                    CASE WHEN rh.is_verified = 1 THEN rh.point ELSE 0 END AS credit_point,
                    0 AS catalogue_point,
                    0 AS tds_point,
                    CASE WHEN rh.is_verified != 1 THEN rh.point ELSE 0 END AS debit_point,

                    rh.description,

                    NULL AS address1,
                    NULL AS address2,
                    NULL AS city,
                    NULL AS district,
                    NULL AS state,
                    NULL AS pincode,
                    NULL AS phone,

                    rh.created_at,
                    rh.reward_date_time AS ledger_date,
                    l.lifting_date
                FROM reward_history rh
                LEFT JOIN lifting l ON rh.lifting_id = l.id
                WHERE rh.user_id IN ($idList)
                AND rh.is_eligible_for_ledger = 1
                AND rh.reward_date_time BETWEEN '$fromDate' AND '$toDate'

                UNION ALL

                /* ---------- 4. Rejected Redeem ---------- */
                SELECT 
                    rr.id,
                    NULL AS order_id,
                    rr.user_id,

                    rr.point_credited AS credit_point,
                    0 AS catalogue_point,
                    0 AS tds_point,
                    0 AS debit_point,

                    rr.description,

                    NULL AS address1,
                    NULL AS address2,
                    NULL AS city,
                    NULL AS district,
                    NULL AS state,
                    NULL AS pincode,
                    NULL AS phone,

                    rr.created_at,
                    rr.created_at AS ledger_date,
                    NULL AS lifting_date
                FROM rejected_redeemtions rr
                WHERE rr.user_id IN ($idList)
                AND rr.created_at BETWEEN '$fromDate' AND '$toDate'

            ) AS x
            ORDER BY user_id, ledger_date ASC
        ";

        return DB::query()->from(DB::raw("($sql) as ledger"));
    }


    // --------------------------
    // QUERY FOR SINGLE USER
    // --------------------------
    private function querySingleUser($idList, $fromDate, $toDate)
    {
        // $sql = "
        //     WITH full_ledger AS (

        //         -- 1. Catalogue Redeem
        //         SELECT 
        //             ucr.id,
        //             get_mason_details(ucr.user_id) AS mason_details,
        //             IFNULL(ucr.order_id, '') AS order_id,
        //             ucr.user_id,
        //             0 AS credit_point,
        //             ucr.redeemed_point AS debit_point,
        //             IFNULL(ucr.description, '') AS description,
        //             ucr.created_at,
        //             ucr.created_at AS ledger_date,
        //             NULL AS lifting_date
        //         FROM user_catalogue_redeemtions ucr
        //         WHERE ucr.user_id IN ($idList)

        //         UNION ALL

        //         -- 2. Rewards
        //         SELECT 
        //             r.id,
        //             get_mason_details(r.user_id) AS mason_details,
        //             NULL AS order_id,
        //             r.user_id,
        //             CASE WHEN r.is_verified = 1 THEN r.point ELSE 0 END AS credit_point,
        //             CASE WHEN r.is_verified != 1 THEN r.point ELSE 0 END AS debit_point,
        //             r.description,
        //             r.created_at,
        //             r.updated_at AS ledger_date,
        //             l.lifting_date
        //         FROM rewards r
        //         LEFT JOIN lifting l ON r.lifting_id = l.id
        //         WHERE r.user_id IN ($idList)
        //         AND r.is_eligible_for_ledger = 1

        //         UNION ALL

        //         -- 3. Reward History
        //         SELECT 
        //             rh.id,
        //             get_mason_details(rh.user_id) AS mason_details,
        //             NULL AS order_id,
        //             rh.user_id,
        //             CASE WHEN rh.is_verified = 1 THEN rh.point ELSE 0 END AS credit_point,
        //             CASE WHEN rh.is_verified != 1 THEN rh.point ELSE 0 END AS debit_point,
        //             rh.description,
        //             rh.created_at,
        //             rh.reward_date_time AS ledger_date,
        //             l.lifting_date
        //         FROM reward_history rh
        //         LEFT JOIN lifting l ON rh.lifting_id = l.id
        //         WHERE rh.user_id IN ($idList)
        //         AND rh.is_eligible_for_ledger = 1

        //         UNION ALL

        //         -- 4. Rejected Redeem
        //         SELECT 
        //             rr.id,
        //             get_mason_details(rr.user_id) AS mason_details,
        //             NULL AS order_id,
        //             rr.user_id,
        //             rr.point_credited AS credit_point,
        //             0 AS debit_point,
        //             rr.description,
        //             rr.created_at,
        //             rr.created_at AS ledger_date,
        //             NULL AS lifting_date
        //         FROM rejected_redeemtions rr
        //         WHERE rr.user_id IN ($idList)
        //     ),

        //     opening_balance AS (
        //         SELECT 
        //             user_id,
        //             COALESCE(SUM(credit_point - debit_point), 0) AS opening_balance
        //         FROM full_ledger
        //         WHERE ledger_date < '$fromDate'
        //         GROUP BY user_id
        //     ),

        //     filtered_ledger AS (
        //         SELECT 
        //             fl.*,
        //             (credit_point - debit_point) AS txn_balance
        //         FROM full_ledger fl
        //         WHERE fl.ledger_date BETWEEN '$fromDate' AND '$toDate'
        //     ),

        //     running AS (
        //         SELECT 
        //             fl.id,
        //             fl.mason_details,
        //             fl.order_id,
        //             fl.user_id,
        //             fl.credit_point,
        //             fl.debit_point,
        //             fl.description,
        //             fl.created_at,
        //             fl.ledger_date,
        //             fl.lifting_date,
        //             COALESCE(ob.opening_balance, 0) AS opening_balance,
        //             (
        //                 COALESCE(ob.opening_balance, 0) 
        //                 + SUM(fl.txn_balance) OVER (PARTITION BY fl.user_id ORDER BY fl.ledger_date, fl.id)
        //             ) AS remaining_point
        //         FROM filtered_ledger fl
        //         LEFT JOIN opening_balance ob ON fl.user_id = ob.user_id
        //     ),

        //     opening_rows AS (
        //         SELECT
        //             NULL AS id,
        //             get_mason_details(ob.user_id) AS mason_details,
        //             NULL AS order_id,
        //             ob.user_id AS user_id,
        //             0 AS credit_point,
        //             0 AS debit_point,
        //             CONCAT('Opening Balance as of ', '$fromDate') AS description,
        //             DATE_SUB('$fromDate', INTERVAL 1 SECOND) AS created_at,
        //             DATE_SUB('$fromDate', INTERVAL 1 SECOND) AS ledger_date,
        //             NULL AS lifting_date,
        //             ob.opening_balance AS opening_balance,
        //             ob.opening_balance AS remaining_point
        //         FROM opening_balance ob
        //     )

        //     SELECT * FROM opening_rows
        //     UNION ALL
        //     SELECT * FROM running
        //     ORDER BY user_id, ledger_date ASC
        //     ";

       $sql ="
                WITH full_ledger AS (

                    /* ---------- 1. Catalogue Redeem ---------- */
                    SELECT 
                        ucr.id,
                        get_mason_details(ucr.user_id) AS mason_details,
                        IFNULL(ucr.order_id, '') AS order_id,
                        ucr.user_id,

                        0 AS credit_point,
                        ucr.catalogue_point AS catalogue_point,
                        ucr.catalogue_tds_point AS tds_point,
                        ucr.redeemed_point AS debit_point,

                        IFNULL(ucr.description, '') AS description,

                        ucr.address1,
                        ucr.address2,
                        ucr.city,
                        ucr.district,
                        ucr.state,
                        ucr.pincode,
                        ucr.phone,

                        ucr.created_at,
                        ucr.created_at AS ledger_date,
                        NULL AS lifting_date
                    FROM user_catalogue_redeemtions ucr
                    WHERE ucr.user_id IN ($idList)

                    UNION ALL

                    /* ---------- 2. Rewards ---------- */
                    SELECT 
                        r.id,
                        get_mason_details(r.user_id) AS mason_details,
                        NULL AS order_id,
                        r.user_id,

                        CASE WHEN r.is_verified = 1 THEN r.point ELSE 0 END AS credit_point,
                        0 AS catalogue_point,
                        0 AS tds_point,
                        CASE WHEN r.is_verified != 1 THEN r.point ELSE 0 END AS debit_point,

                        r.description,

                        NULL AS address1,
                        NULL AS address2,
                        NULL AS city,
                        NULL AS district,
                        NULL AS state,
                        NULL AS pincode,
                        NULL AS phone,

                        r.created_at,
                        r.updated_at AS ledger_date,
                        l.lifting_date
                    FROM rewards r
                    LEFT JOIN lifting l ON r.lifting_id = l.id
                    WHERE r.user_id IN ($idList)
                    AND r.is_eligible_for_ledger = 1

                    UNION ALL

                    /* ---------- 3. Reward History ---------- */
                    SELECT 
                        rh.id,
                        get_mason_details(rh.user_id) AS mason_details,
                        NULL AS order_id,
                        rh.user_id,

                        CASE WHEN rh.is_verified = 1 THEN rh.point ELSE 0 END AS credit_point,
                        0 AS catalogue_point,
                        0 AS tds_point,
                        CASE WHEN rh.is_verified != 1 THEN rh.point ELSE 0 END AS debit_point,

                        rh.description,

                        NULL AS address1,
                        NULL AS address2,
                        NULL AS city,
                        NULL AS district,
                        NULL AS state,
                        NULL AS pincode,
                        NULL AS phone,

                        rh.created_at,
                        rh.reward_date_time AS ledger_date,
                        l.lifting_date
                    FROM reward_history rh
                    LEFT JOIN lifting l ON rh.lifting_id = l.id
                    WHERE rh.user_id IN ($idList)
                    AND rh.is_eligible_for_ledger = 1

                    UNION ALL

                    /* ---------- 4. Rejected Redeem ---------- */
                    SELECT 
                        rr.id,
                        get_mason_details(rr.user_id) AS mason_details,
                        NULL AS order_id,
                        rr.user_id,

                        rr.point_credited AS credit_point,
                        0 AS catalogue_point,
                        0 AS tds_point,
                        0 AS debit_point,

                        rr.description,

                        NULL AS address1,
                        NULL AS address2,
                        NULL AS city,
                        NULL AS district,
                        NULL AS state,
                        NULL AS pincode,
                        NULL AS phone,

                        rr.created_at,
                        rr.created_at AS ledger_date,
                        NULL AS lifting_date
                    FROM rejected_redeemtions rr
                    WHERE rr.user_id IN ($idList)
                ),

                /* ---------- OPENING BALANCE ---------- */
                opening_balance AS (
                    SELECT 
                        user_id,
                        COALESCE(SUM(credit_point - debit_point), 0) AS opening_balance
                    FROM full_ledger
                    WHERE ledger_date < '$fromDate'
                    GROUP BY user_id
                ),

                /* ---------- FILTERED LEDGER ---------- */
                filtered_ledger AS (
                    SELECT 
                        fl.*,
                        (credit_point - debit_point) AS txn_balance
                    FROM full_ledger fl
                    WHERE fl.ledger_date BETWEEN '$fromDate' AND '$toDate'
                ),

                /* ---------- RUNNING BALANCE ---------- */
                running AS (
                    SELECT 
                        fl.id,
                        fl.mason_details,
                        fl.order_id,
                        fl.user_id,

                        fl.credit_point,
                        fl.catalogue_point,
                        fl.tds_point,
                        fl.debit_point,
                        fl.description,

                        fl.address1,
                        fl.address2,
                        fl.city,
                        fl.district,
                        fl.state,
                        fl.pincode,
                        fl.phone,

                        fl.created_at,
                        fl.ledger_date,
                        fl.lifting_date,

                        COALESCE(ob.opening_balance, 0) AS opening_balance,
                        (
                            COALESCE(ob.opening_balance, 0)
                            + SUM(fl.txn_balance) OVER (
                                PARTITION BY fl.user_id
                                ORDER BY fl.ledger_date, fl.id
                            )
                        ) AS remaining_point
                    FROM filtered_ledger fl
                    LEFT JOIN opening_balance ob ON fl.user_id = ob.user_id
                ),

                /* ---------- OPENING ROW ---------- */
                opening_rows AS (
                    SELECT
                        NULL AS id,
                        get_mason_details(ob.user_id) AS mason_details,
                        NULL AS order_id,
                        ob.user_id,

                        0 AS credit_point,
                        0 AS catalogue_point,
                        0 AS tds_point,
                        0 AS debit_point,

                        CONCAT('Opening Balance as of ', '$fromDate') AS description,

                        NULL AS address1,
                        NULL AS address2,
                        NULL AS city,
                        NULL AS district,
                        NULL AS state,
                        NULL AS pincode,
                        NULL AS phone,

                        DATE_SUB('$fromDate', INTERVAL 1 SECOND) AS created_at,
                        DATE_SUB('$fromDate', INTERVAL 1 SECOND) AS ledger_date,
                        NULL AS lifting_date,

                        ob.opening_balance AS opening_balance,
                        ob.opening_balance AS remaining_point
                    FROM opening_balance ob
                )

                SELECT * FROM opening_rows
                UNION ALL
                SELECT * FROM running
                ORDER BY user_id, ledger_date ASC
            "; 

        return DB::query()->from(DB::raw("($sql) as ledger"));
    }


    public function html()
    {
        return $this->builder()
            ->setTableId('mason-ledger-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                'ordering' => false,
                'searching' => false,
                'stateSave' => false,
                'dom' => 'frtip'
            ]);
    }

    protected function getColumns()
    {
        return [
            ['data' => 'ledger_date', 'title' => 'Date'],
            ['data' => 'mason_details', 'title' => 'Mason'],
            ['data' => 'order_id', 'title' => 'Order Id'],
            ['data' => 'phone', 'title' => 'Phone'],
            ['data' => 'address', 'title' => 'Address'],
            // ['data' => 'address1', 'title' => 'Address1'],
            // ['data' => 'address2', 'title' => 'Address2'],
            // ['data' => 'city', 'title' => 'City'],
            // ['data' => 'district', 'title' => 'District'],
            // ['data' => 'state', 'title' => 'State'],
            // ['data' => 'pincode', 'title' => 'Pincode'],
            ['data' => 'description', 'title' => 'Description'],
            ['data' => 'credit_point', 'title' => 'Credit'],
            ['data' => 'catalogue_point', 'title' => 'Catalogue Point'],
            ['data' => 'tds_point', 'title' => 'TDS Point'],
            ['data' => 'debit_point', 'title' => 'Debit'],
            ['data' => 'remaining_point', 'title' => 'Balance'],
        ];
    }
}
