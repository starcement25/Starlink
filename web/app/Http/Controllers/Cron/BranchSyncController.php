<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\SyncLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log as LaravelLog;

class BranchSyncController extends Controller
{
  
    public function syncBranchesFromApi(Request $request)
    {
        $cronToken = (string) config('services.branch_sync.cron_token', '');

        if ($cronToken !== '' && $request->query('token') !== $cronToken) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $url = "https://starsaathi.com/SAP/branch_list_api.php";

        if ($url === '') {
            return response()->json([
                'status' => false,
                'message' => 'Missing BRANCH_SYNC_API_URL in env.'
            ], 500);
        }

        set_time_limit(0);

        $limit = 100;
        $timeout = 60;
        $maxPages = 0;

        $apiToken = '';
        $apiHeader = '';

        $page = 1;

        $pagesProcessed = 0;
        $totalFetched = 0;
        $updatedCount = 0;
        $createdCount = 0;
        $notFoundCount = 0;
        $errorCount = 0;

        try {

            $totalPagesFromApi = null;

            while (true) {

                if ($maxPages > 0 && $page > $maxPages) {
                    break;
                }

                $http = Http::timeout($timeout);

                if ($apiToken !== '') {

                    if ($apiHeader !== '') {
                        $http = $http->withHeaders([
                            $apiHeader => $apiToken
                        ]);
                    } else {
                        $http = $http->withToken($apiToken);
                    }
                }

                $baseUrl = $url;
                $existingQueryParams = [];

                if (str_contains($url, '?')) {

                    $baseUrl = (string) strtok($url, '?');

                    $query = (string) parse_url($url, PHP_URL_QUERY);

                    if ($query !== '') {
                        parse_str($query, $existingQueryParams);
                    }
                }

                $response = $http->get($baseUrl, array_merge(
                    $existingQueryParams,
                    [
                        'page' => $page,
                        'limit' => $limit,
                    ]
                ));

                if (!$response->ok()) {

                    $errorCount++;

                    LaravelLog::error('Branch Sync API failed', [
                        'page' => $page,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    break;
                }

                $payload = $response->json();

                $data = $payload['data'] ?? null;

                if (!is_array($data)) {

                    $errorCount++;

                    LaravelLog::error('Branch Sync API invalid payload', [
                        'page' => $page,
                        'payload' => $payload
                    ]);

                    break;
                }

                if (
                    $totalPagesFromApi === null &&
                    array_key_exists('total_pages', $payload)
                ) {
                    $totalPagesFromApi = (int) $payload['total_pages'];
                }

                $pagesProcessed++;

                /**
                 * REMOVE DUPLICATE dns_branch_code
                 */
                $uniqueData = [];

                foreach ($data as $row) {

                    $dnsBranchCode = trim((string) ($row['dns_branch_code'] ?? ''));

                    if ($dnsBranchCode === '') {
                        continue;
                    }

                    /**
                     * KEEP ONLY FIRST RECORD
                     */
                    if (!isset($uniqueData[$dnsBranchCode])) {
                        $uniqueData[$dnsBranchCode] = $row;
                    }
                }

                $data = array_values($uniqueData);

                $totalFetched += count($data);

                if (count($data) === 0) {
                    break;
                }

                $dnsBranchCodes = [];

                foreach ($data as $row) {

                    $dnsBranchCode = trim((string) ($row['dns_branch_code'] ?? ''));

                    if ($dnsBranchCode !== '') {
                        $dnsBranchCodes[] = $dnsBranchCode;
                    }
                }

                $dnsBranchCodes = array_values(array_unique($dnsBranchCodes));

                $branchesByDnsCode = Branch::whereIn(
                    'branch_code',
                    $dnsBranchCodes
                )
                ->lockForUpdate()
                ->get()
                ->keyBy(function ($branch) {
                    return (string) $branch->branch_code;
                });

                foreach ($data as $row) {

                    $dnsBranchCode = trim((string) ($row['dns_branch_code'] ?? ''));

                    if ($dnsBranchCode === '') {
                        continue;
                    }

                    $status = null;

                    if (is_array($row) && array_key_exists('acedns', $row)) {

                        $acedns = strtoupper(
                            trim((string) $row['acedns'])
                        );

                        $status = $acedns === 'Y' ? 1 : 0;
                    }

                    /** @var \App\Models\Branch|null $branch */
                    $branch = $branchesByDnsCode->get($dnsBranchCode);

                    if (!$branch) {

                        $branchName = $row['branch_name'] ?? null;
                        $sathiCode = $row['branch_code'] ?? null;

                        $createData = [
                            'branch_code' => $dnsBranchCode,
                            'status' => $status ?? 1,
                        ];

                        if (
                            $branchName !== null &&
                            (string) $branchName !== ''
                        ) {
                            $createData['name'] = (string) $branchName;
                        }

                        if (
                            $sathiCode !== null &&
                            (string) $sathiCode !== ''
                        ) {
                            $createData['sathi_code'] = (string) $sathiCode;
                        }

                        try {

                            DB::beginTransaction();

                            /**
                             * RECHECK INSIDE TRANSACTION
                             * TO PREVENT DUPLICATE INSERT
                             */
                            $branch = Branch::where(
                                'branch_code',
                                $dnsBranchCode
                            )->lockForUpdate()->first();

                            if (!$branch) {

                                $branch = Branch::create($createData);

                                $createdCount++;

                                SyncLog::create([
                                    'user_id' => 0,
                                    'action' => 'Branch Sync (Created)',
                                    'model_name' => 'Branch',
                                    'request' => json_encode([
                                        'dns_branch_code' => $dnsBranchCode,
                                        'api' => $row
                                    ]),
                                    'response' => json_encode([
                                        'status' => 'created',
                                        'created' => $createData,
                                    ]),
                                ]);
                            }

                            DB::commit();

                            $branchesByDnsCode->put(
                                $dnsBranchCode,
                                $branch
                            );

                        } catch (\Throwable $e) {

                            DB::rollBack();

                            $notFoundCount++;
                            $errorCount++;

                            LaravelLog::error(
                                'Branch Sync branch create failed',
                                [
                                    'dns_branch_code' => $dnsBranchCode,
                                    'exception' => get_class($e),
                                    'message' => $e->getMessage(),
                                ]
                            );

                            SyncLog::create([
                                'user_id' => 0,
                                'action' => 'Branch Sync (Create Failed)',
                                'model_name' => 'Branch',
                                'request' => json_encode([
                                    'dns_branch_code' => $dnsBranchCode,
                                    'api' => $row
                                ]),
                                'response' => json_encode([
                                    'status' => 'create_failed',
                                    'exception' => get_class($e),
                                    'message' => $e->getMessage(),
                                    'attempted' => $createData,
                                ]),
                            ]);

                            continue;
                        }
                    }

                    $updateData = [];

                    $branchName = $row['branch_name'] ?? null;

                    if (
                        $branchName !== null &&
                        (string) $branchName !== ''
                    ) {
                        $updateData['name'] = (string) $branchName;
                    }

                    $sathiCode = $row['branch_code'] ?? null;

                    if (
                        $sathiCode !== null &&
                        (string) $sathiCode !== ''
                    ) {
                        $updateData['sathi_code'] = (string) $sathiCode;
                    }

                    if ($status !== null) {
                        $updateData['status'] = $status;
                    }

                    if (count($updateData) === 0) {
                        continue;
                    }

                    $previous = Arr::only(
                        $branch->getAttributes(),
                        array_keys($updateData)
                    );

                    $branch->fill($updateData);

                    if (!$branch->isDirty()) {

                        SyncLog::create([
                            'user_id' => 0,
                            'action' => 'Branch Sync (No Changes)',
                            'model_name' => 'Branch',
                            'request' => json_encode([
                                'dns_branch_code' => $dnsBranchCode,
                                'api' => $row,
                                'attempted' => $updateData
                            ]),
                            'response' => json_encode([
                                'status' => 'no_change'
                            ]),
                        ]);

                        continue;
                    }

                    $new = Arr::only(
                        array_merge($previous, $updateData),
                        array_keys($updateData)
                    );

                    try {

                        $branch->save();

                        $updatedCount++;

                        SyncLog::create([
                            'user_id' => 0,
                            'action' => 'Branch Sync (Updated)',
                            'model_name' => 'Branch',
                            'request' => json_encode([
                                'dns_branch_code' => $dnsBranchCode,
                                'api' => $row
                            ]),
                            'response' => json_encode([
                                'status' => 'updated',
                                'previous' => $previous,
                                'new' => $new,
                            ]),
                        ]);

                    } catch (\Throwable $e) {

                        $errorCount++;

                        LaravelLog::error(
                            'Branch Sync branch update failed',
                            [
                                'branch_id' => $branch->id,
                                'dns_branch_code' => $dnsBranchCode,
                                'message' => $e->getMessage(),
                            ]
                        );

                        SyncLog::create([
                            'user_id' => 0,
                            'action' => 'Branch Sync (Error)',
                            'model_name' => 'Branch',
                            'request' => json_encode([
                                'dns_branch_code' => $dnsBranchCode,
                                'api' => $row
                            ]),
                            'response' => json_encode([
                                'status' => 'error',
                                'message' => $e->getMessage(),
                                'previous' => $previous,
                                'attempted' => $updateData,
                            ]),
                        ]);
                    }
                }

                if (
                    $totalPagesFromApi !== null &&
                    $totalPagesFromApi > 0
                ) {

                    if ($page >= $totalPagesFromApi) {
                        break;
                    }

                } else {

                    $reportedLimit = (int) (
                        $payload['limit'] ?? $limit
                    );

                    if (count($data) < $reportedLimit) {
                        break;
                    }
                }

                $page++;
            }

        } catch (\Throwable $e) {

            $errorCount++;

            LaravelLog::error('Branch Sync exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        SyncLog::create([
            'user_id' => 0,
            'action' => 'Branch Sync (Summary)',
            'model_name' => 'Branch',
            'request' => json_encode([
                'pages_processed' => $pagesProcessed,
                'total_fetched' => $totalFetched,
                'created' => $createdCount,
                'updated' => $updatedCount,
                'not_found' => $notFoundCount,
                'errors' => $errorCount,
            ]),
            'response' => json_encode([
                'status' => $errorCount === 0
            ]),
        ]);

        return response()->json([
            'status' => $errorCount === 0,
            'pages_processed' => $pagesProcessed,
            'total_fetched' => $totalFetched,
            'created' => $createdCount,
            'updated' => $updatedCount,
            'not_found' => $notFoundCount,
            'errors' => $errorCount,
        ]);
    }
}
