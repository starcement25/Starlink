<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\SyncLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log as LaravelLog;

class DealerSyncController extends Controller
{
    public function syncDealersFromApi(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Prevent Multiple Sync Execution
        |--------------------------------------------------------------------------
        */
        $lock = Cache::lock('dealer-sync-lock', 3600);

        if (!$lock->get()) {
            return response()->json([
                'status' => false,
                'message' => 'Dealer sync already running.',
            ], 429);
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Cron Token Validation
            |--------------------------------------------------------------------------
            */
            $cronToken = (string) config('services.dealer_sync.cron_token', '');

            if (
                $cronToken !== '' &&
                $request->query('token') !== $cronToken
            ) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized.',
                ], 403);
            }

            /*
            |--------------------------------------------------------------------------
            | API URL
            |--------------------------------------------------------------------------
            */
            $url = "https://starsaathi.com/SAP/dealer_status_active-1.php";

            if ($url === '') {
                return response()->json([
                    'status' => false,
                    'message' => 'Missing API URL.',
                ], 500);
            }

            set_time_limit(3600);

            /*
            |--------------------------------------------------------------------------
            | Config
            |--------------------------------------------------------------------------
            */
            $limit = 500;
            $timeout = 60;
            $page = 1;

            /*
            |--------------------------------------------------------------------------
            | Counters
            |--------------------------------------------------------------------------
            */
            $pagesProcessed = 0;
            $totalFetched = 0;
            $createdCount = 0;
            $updatedCount = 0;
            $duplicatePhoneCount = 0;
            $duplicateEmailCount = 0;
            $branchNotFoundCount = 0;
            $linkedDealerNotFoundCount = 0;
            $errorCount = 0;

            /*
            |--------------------------------------------------------------------------
            | Error Dealer ID Collections
            |--------------------------------------------------------------------------
            */
            $duplicatePhoneDealerIds = [];
            $duplicateEmailDealerIds = [];
            $branchNotFoundDealerIds = [];
            $linkedDealerNotFoundDealerIds = [];

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Processing In Same Execution
            |--------------------------------------------------------------------------
            */
            $processedDealerIds = [];

            while (true) {

                /*
                |--------------------------------------------------------------------------
                | API Call
                |--------------------------------------------------------------------------
                */
                $response = Http::timeout($timeout)->get($url, [
                    'page' => $page,
                    'limit' => $limit,
                ]);

                if (!$response->ok()) {

                    $errorCount++;

                    LaravelLog::error('Dealer Sync API Failed', [
                        'page' => $page,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    break;
                }

                $payload = $response->json();

                $data = $payload['data'] ?? [];

                if (!is_array($data)) {

                    $errorCount++;

                    LaravelLog::error('Invalid API Payload', [
                        'page' => $page,
                        'payload' => $payload,
                    ]);

                    break;
                }

                if (empty($data)) {
                    break;
                }

                $pagesProcessed++;
                $totalFetched += count($data);

                /*
                |--------------------------------------------------------------------------
                | Prepare Collections
                |--------------------------------------------------------------------------
                */
                $dealerIds = [];
                $branchCodes = [];
                $emails = [];
                $phones = [];
                $linkedDealerIds = [];

                foreach ($data as $row) {

                    $dealerId = trim((string) ($row['dealer_id'] ?? ''));

                    if ($dealerId !== '') {
                        $dealerIds[] = $dealerId;
                    }

                    $branchCode = trim((string) ($row['branch_code'] ?? ''));

                    if ($branchCode !== '') {
                        $branchCodes[] = $branchCode;
                    }

                    $email = trim((string) ($row['email'] ?? ''));

                    if ($email !== '') {
                        $emails[] = $email;
                    }

                    $phone = trim((string) ($row['phone_no'] ?? ''));

                    if ($phone !== '') {
                        $phones[] = $phone;
                    }

                    $linkedDealer = trim((string) ($row['linked_dealer'] ?? ''));

                    if ($linkedDealer !== '') {
                        $linkedDealerIds[] = $linkedDealer;
                    }
                }

                $dealerIds = array_unique($dealerIds);
                $branchCodes = array_unique($branchCodes);
                $emails = array_unique($emails);
                $phones = array_unique($phones);
                $linkedDealerIds = array_unique($linkedDealerIds);

                /*
                |--------------------------------------------------------------------------
                | Bulk Fetch
                |--------------------------------------------------------------------------
                */
                $usersBySapCode = User::whereIn('sap_code', $dealerIds)
                    ->select(
                        'id',
                        'sap_code',
                        'email',
                        'phone',
                        'linked_dealer',
                        'branch_id',
                        'name',
                        'status',
                        'role'
                    )
                    ->get()
                    ->keyBy('sap_code');

                $linkedDealersBySapCode = User::whereIn('sap_code', $linkedDealerIds)
                    ->select('id', 'sap_code')
                    ->get()
                    ->keyBy('sap_code');

                $branchIdByCode = Branch::whereIn('sathi_code', $branchCodes)
                    ->where('status', 1)
                    ->pluck('id', 'sathi_code')
                    ->toArray();

                $existingEmails = User::whereIn('email', $emails)
                    ->pluck('id', 'email')->toArray();

                $existingPhones = User::whereIn('phone', $phones)
                    ->pluck('id', 'phone')->toArray();

                /*
                |--------------------------------------------------------------------------
                | Process Rows
                |--------------------------------------------------------------------------
                */
                foreach ($data as $row) {

                    $dealerId = trim((string) ($row['dealer_id'] ?? ''));

                    if ($dealerId === '') {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Prevent Duplicate Processing
                    |--------------------------------------------------------------------------
                    */
                    if (isset($processedDealerIds[$dealerId])) {
                        continue;
                    }

                    $processedDealerIds[$dealerId] = true;

                    /*
                    |--------------------------------------------------------------------------
                    | Status
                    |--------------------------------------------------------------------------
                    */
                    $acedns = strtoupper(trim((string) ($row['acedns'] ?? 'N')));

                    $status = $acedns === 'Y' ? 1 : 0;

                    /*
                    |--------------------------------------------------------------------------
                    | Existing User
                    |--------------------------------------------------------------------------
                    */
                    $user = $usersBySapCode->get($dealerId);

                    /*
                    |--------------------------------------------------------------------------
                    | Fallback Database Lookup
                    |--------------------------------------------------------------------------
                    */
                    if (!$user) {

                        $user = User::select(
                                'id',
                                'sap_code',
                                'email',
                                'phone',
                                'linked_dealer',
                                'branch_id',
                                'name',
                                'status',
                                'role'
                            )
                            ->where('sap_code', $dealerId)
                            ->first();

                        /*
                        |--------------------------------------------------------------------------
                        | Update Local Cache
                        |--------------------------------------------------------------------------
                        */
                        if ($user) {

                            $usersBySapCode->put(
                                (string) $user->sap_code,
                                $user
                            );
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Common Data
                    |--------------------------------------------------------------------------
                    */
                    $branchCode = trim((string) ($row['branch_code'] ?? ''));

                    $branchName = trim((string) ($row['branch_name'] ?? ''));

                    $zone = trim((string) ($row['zone'] ?? ''));

                    /*
                    |--------------------------------------------------------------------------
                    | Create User
                    |--------------------------------------------------------------------------
                    */
                    if (!$user) {

                        DB::beginTransaction();

                        try {

                            $email = trim((string) ($row['email'] ?? ''));
                            $email = $email !== '' ? $email : null;

                            if ($email === '') {
                                $email = null;
                            }

                            $phone = trim((string) ($row['phone_no'] ?? ''));
                            $phone = $phone !== '' ? $phone : null;

                            $whatsapp = trim((string) ($row['whatsapp_no'] ?? ''));
                            $whatsapp = $whatsapp !== '' ? $whatsapp : null;

                            $name = trim((string) ($row['customer_name'] ?? ''));

                            $empCode = trim((string) ($row['dns_customer_code'] ?? ''));
                            $empCode = $empCode !== '' ? $empCode : null;

                            $custType = strtoupper(trim((string) ($row['cust_type'] ?? '')));

                            /*
                            |--------------------------------------------------------------------------
                            | Role
                            |--------------------------------------------------------------------------
                            */
                            $role = User::ROLE_DEALER;

                            if ($custType === 'RSSD') {
                                $role = User::ROLE_RSSD;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Duplicate Email
                            |--------------------------------------------------------------------------
                            */
                            if (
                                $email !== null &&
                                isset($existingEmails[$email])
                            ) {

                                $duplicateEmailCount++;
                                $duplicateEmailDealerIds[] = [
                                    'dealer_id' => $dealerId,
                                    'email' => $email,
                                ];

                                $email = null;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Duplicate Phone
                            |--------------------------------------------------------------------------
                            */
                            if (
                                $phone !== null &&
                                isset($existingPhones[$phone])
                            ) {

                                $duplicatePhoneCount++;
                                $duplicatePhoneDealerIds[] = [
                                        'dealer_id' => $dealerId,
                                        'phone' => $phone,
                                    ];

                                $phone = null;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Create Data
                            |--------------------------------------------------------------------------
                            */
                            $createData = [
                                'sap_code' => $dealerId,
                                'emp_code' => $empCode,
                                'name' => $name ?: ('Dealer ' . $dealerId),
                                'email' => $email,
                                'phone' => $phone,
                                'whatsapp_no' => $whatsapp,
                                'role' => $role,
                                'status' => $status,
                                'otp_type' => 1,
                                'created_by' => 0,
                            ];

                            /*
                            |--------------------------------------------------------------------------
                            | Branch Handling
                            |--------------------------------------------------------------------------
                            */
                            if ($branchCode !== '') {

                                $branchId = $branchIdByCode[$branchCode] ?? null;

                                /*
                                |--------------------------------------------------------------------------
                                | Fallback Database Lookup
                                |--------------------------------------------------------------------------
                                */
                                if (!$branchId) {

                                    $branch = Branch::select('id', 'sathi_code')
                                        ->where('sathi_code', $branchCode)
                                        ->where('status', 1)
                                        ->first();

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Update Local Cache
                                    |--------------------------------------------------------------------------
                                    */
                                    if ($branch) {

                                        $branchId = $branch->id;

                                        $branchIdByCode[$branchCode] = $branch->id;
                                    }
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | Final Safety Check
                                |--------------------------------------------------------------------------
                                */
                                if (!$branchId) {

                                    $branchNotFoundCount++;
                                    $branchNotFoundDealerIds[] = [
                                        'dealer_id' => $dealerId,
                                        'branch_code' => $branchCode,
                                    ];

                                    SyncLog::create([
                                        'user_id' => 0,
                                        'action' => 'Dealer Sync (Skipped - Branch Not Found)',
                                        'model_name' => 'User',
                                        'request' => json_encode([
                                            'dealer_id' => $dealerId,
                                            'api' => $row,
                                        ]),
                                        'response' => json_encode([
                                            'status' => 'skipped',
                                            'reason' => 'branch_not_found',
                                            'branch_code' => $branchCode,
                                            'branch_name' => $branchName,
                                            'zone' => $zone,
                                        ]),
                                    ]);

                                    DB::rollBack();

                                    continue;
                                }

                                $createData['branch_id'] = (int) $branchId;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Linked Dealer Handling
                            |--------------------------------------------------------------------------
                            */
                            $linkedDealerProvided = is_array($row)
                                && array_key_exists('linked_dealer', $row);

                            $linkedDealer = $linkedDealerProvided
                                ? trim((string) $row['linked_dealer'])
                                : '';

                            if ($linkedDealerProvided) {

                                if ($linkedDealer === '') {

                                    $createData['linked_dealer'] = null;

                                } else {

                                    $linkedDealerUser = $linkedDealersBySapCode->get($linkedDealer);

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Fallback Database Lookup
                                    |--------------------------------------------------------------------------
                                    */
                                    if (!$linkedDealerUser) {

                                        $linkedDealerUser = User::select('id', 'sap_code')
                                            ->where('sap_code', $linkedDealer)
                                            ->first();

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Update Local Cache
                                        |--------------------------------------------------------------------------
                                        */
                                        if ($linkedDealerUser) {

                                            $linkedDealersBySapCode->put(
                                                (string) $linkedDealerUser->sap_code,
                                                $linkedDealerUser
                                            );
                                        }
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Final Safety Check
                                    |--------------------------------------------------------------------------
                                    */
                                    if (!$linkedDealerUser || !$linkedDealerUser->id) {

                                        $linkedDealerNotFoundCount++;
                                        $linkedDealerNotFoundDealerIds[] = [
                                            'dealer_id' => $dealerId,
                                            'linked_dealer' => $linkedDealer,
                                        ];

                                        SyncLog::create([
                                            'user_id' => 0,
                                            'action' => 'Dealer Sync (Skipped - Linked Dealer Not Found)',
                                            'model_name' => 'User',
                                            'request' => json_encode([
                                                'dealer_id' => $dealerId,
                                                'api' => $row,
                                            ]),
                                            'response' => json_encode([
                                                'status' => 'skipped',
                                                'reason' => 'linked_dealer_not_found',
                                                'linked_dealer_sap_code' => $linkedDealer,
                                            ]),
                                        ]);

                                        DB::rollBack();

                                        continue;
                                    }

                                    $createData['linked_dealer'] = (int) $linkedDealerUser->id;
                                }
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Final Duplicate Safety
                            |--------------------------------------------------------------------------
                            */
                            $existingUser = User::where('sap_code', $dealerId)->first();

                            if ($existingUser) {

                                $user = $existingUser;

                            } else {

                                $user = User::create($createData);

                                $createdCount++;

                                if (!empty($user->email)) {
                                    $existingEmails[$user->email] = $user->id;
                                }

                                if (!empty($user->phone)) {
                                    $existingPhones[$user->phone] = $user->id;
                                }

                                SyncLog::create([
                                    'user_id' => $user->id,
                                    'action' => 'Dealer Sync (Created)',
                                    'model_name' => 'User',
                                    'request' => json_encode([
                                        'dealer_id' => $dealerId,
                                        'api' => $row,
                                    ]),
                                    'response' => json_encode([
                                        'status' => 'created',
                                        'created' => $createData,
                                        'api_context' => [
                                            'branch_code' => $branchCode,
                                            'branch_name' => $branchName,
                                            'zone' => $zone,
                                        ],
                                    ]),
                                ]);
                            }

                            DB::commit();

                            /*
                            |--------------------------------------------------------------------------
                            | Update Local Cache
                            |--------------------------------------------------------------------------
                            */
                            $usersBySapCode->put(
                                (string) $user->sap_code,
                                $user
                            );

                        } catch (\Throwable $e) {

                            DB::rollBack();

                            $errorCount++;

                            LaravelLog::error('Dealer Create Failed', [
                                'dealer_id' => $dealerId,
                                'message' => $e->getMessage(),
                                'line' => $e->getLine(),
                                'file' => $e->getFile(),
                            ]);

                            continue;
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Update Data
                    |--------------------------------------------------------------------------
                    */
                    $updateData = [];

                    $name = trim((string) ($row['customer_name'] ?? ''));

                    if ($name !== '') {
                        $updateData['name'] = $name;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Role Update
                    |--------------------------------------------------------------------------
                    */
                    $custType = strtoupper(trim((string) ($row['cust_type'] ?? '')));

                    if ($custType !== '') {

                        if ($custType === 'RSSD') {

                            $updateData['role'] = User::ROLE_RSSD;

                        } elseif ($custType === 'DEALER') {

                            $updateData['role'] = User::ROLE_DEALER;
                        }
                    }

                    $email = trim((string) ($row['email'] ?? ''));
                    $email = $email !== '' ? $email : null;

                    if (
                        $email !== null &&
                        !User::where('email', $email)
                            ->where('id', '!=', $user->id)
                            ->exists()
                    ){
                        $updateData['email'] = $email;
                    }else{

                        $duplicateEmailCount++;

                        $duplicateEmailDealerIds[] = $dealerId;

                        SyncLog::create([
                            'user_id' => $user->id,
                            'action' => 'Dealer Sync (Duplicate Email Skipped)',
                            'model_name' => 'User',
                            'request' => json_encode([
                                'dealer_id' => $dealerId,
                                'api' => $row,
                            ]),
                            'response' => json_encode([
                                'status' => 'skipped',
                                'reason' => 'duplicate_email',
                                'email' => $email,
                            ]),
                        ]);

                    }

                    $phone = trim((string) ($row['phone_no'] ?? ''));
                    $phone = $phone !== '' ? $phone : null;

                    if (
                        $phone !== null &&
                        !User::where('phone', $phone)
                            ->where('id', '!=', $user->id)
                            ->exists()
                    ){
                        $updateData['phone'] = $phone;
                    }else {
                       $duplicatePhoneCount++;

                        $duplicatePhoneDealerIds[] = $dealerId;

                        SyncLog::create([
                            'user_id' => $user->id,
                            'action' => 'Dealer Sync (Duplicate Phone Skipped)',
                            'model_name' => 'User',
                            'request' => json_encode([
                                'dealer_id' => $dealerId,
                                'api' => $row,
                            ]),
                            'response' => json_encode([
                                'status' => 'skipped',
                                'reason' => 'duplicate_phone',
                                'phone' => $phone,
                            ]),
                        ]);
                    }

                    $whatsapp = trim((string) ($row['whatsapp_no'] ?? ''));
                    $whatsapp = $whatsapp !== '' ? $whatsapp : null;

                    if ($whatsapp !== null) {
                        $updateData['whatsapp_no'] = $whatsapp;
                    }

                    $updateData['status'] = $status;

                    /*
                    |--------------------------------------------------------------------------
                    | Branch Handling
                    |--------------------------------------------------------------------------
                    */
                    if ($branchCode !== '') {

                        $branchId = $branchIdByCode[$branchCode] ?? null;

                        if (!$branchId) {

                            $branch = Branch::select('id', 'sathi_code')
                                ->where('status', 1)
                                ->where('sathi_code', $branchCode)
                                ->first();

                            if ($branch) {

                                $branchId = $branch->id;

                                $branchIdByCode[$branchCode] = $branch->id;
                            }
                        }

                        if (!$branchId) {

                            $branchNotFoundCount++;
                            $branchNotFoundDealerIds[] = [
                                'dealer_id' => $dealerId,
                                'branch_code' => $branchCode,
                            ];

                            SyncLog::create([
                                'user_id' => $user->id,
                                'action' => 'Dealer Sync (Skipped - Branch Not Found)',
                                'model_name' => 'User',
                                'request' => json_encode([
                                    'dealer_id' => $dealerId,
                                    'api' => $row,
                                ]),
                                'response' => json_encode([
                                    'status' => 'skipped',
                                    'reason' => 'branch_not_found',
                                    'branch_code' => $branchCode,
                                    'branch_name' => $branchName,
                                    'zone' => $zone,
                                ]),
                            ]);

                            continue;
                        }

                        $updateData['branch_id'] = (int) $branchId;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Linked Dealer Handling
                    |--------------------------------------------------------------------------
                    */
                    $linkedDealerProvided = is_array($row)
                        && array_key_exists('link_dealer', $row);

                    $linkedDealer = $linkedDealerProvided
                        ? trim((string) $row['link_dealer'])
                        : '';

                    if ($linkedDealerProvided) {

                        if ($linkedDealer === '') {

                            $updateData['linked_dealer'] = null;

                        } else {

                            $linkedDealerUser = $linkedDealersBySapCode->get($linkedDealer);

                            if (!$linkedDealerUser) {

                                $linkedDealerUser = User::select('id', 'sap_code')
                                    ->where('sap_code', $linkedDealer)
                                    ->first();

                                if ($linkedDealerUser) {

                                    $linkedDealersBySapCode->put(
                                        (string) $linkedDealerUser->sap_code,
                                        $linkedDealerUser
                                    );
                                }
                            }

                            if (!$linkedDealerUser || !$linkedDealerUser->id) {

                                $linkedDealerNotFoundCount++;
                                $linkedDealerNotFoundDealerIds[] = [
                                    'dealer_id' => $dealerId,
                                    'linked_dealer' => $linkedDealer,
                                ];

                                SyncLog::create([
                                    'user_id' => $user->id,
                                    'action' => 'Dealer Sync (Skipped - Linked Dealer Not Found)',
                                    'model_name' => 'User',
                                    'request' => json_encode([
                                        'dealer_id' => $dealerId,
                                        'api' => $row,
                                    ]),
                                    'response' => json_encode([
                                        'status' => 'skipped',
                                        'reason' => 'linked_dealer_not_found',
                                        'linked_dealer_sap_code' => $linkedDealer,
                                    ]),
                                ]);

                                continue;
                            }

                            $updateData['linked_dealer'] = (int) $linkedDealerUser->id;
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Skip Empty Updates
                    |--------------------------------------------------------------------------
                    */
                    if (empty($updateData)) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Save Only If Dirty
                    |--------------------------------------------------------------------------
                    */
                    $previous = Arr::only($user->getAttributes(), array_keys($updateData));

                    $oldEmail = $user->email;
                    $oldPhone = $user->phone;

                    $user->fill($updateData);

                    if (!$user->isDirty()) {
                         SyncLog::create([
                            'user_id' => $user->id,
                            'action' => 'Dealer Sync (No Changes)',
                            'model_name' => 'User',
                            'request' => json_encode(['dealer_id' => $dealerId, 'api' => $row, 'attempted' => $updateData]),
                            'response' => json_encode([
                                'status' => 'no_change',
                                'api_context' => [
                                    'branch_code' => $branchCode,
                                    'branch_name' => $branchName,
                                    'zone' => $zone,
                                ],

                            ]),
                        ]);
                        continue;
                    }

                    $new = Arr::only(array_merge($previous, $updateData), array_keys($updateData));

                    DB::beginTransaction();
                    try {

                        $user->save();


                        SyncLog::create([
                            'user_id' => $user->id,
                            'action' => 'Dealer Sync (Updated)',
                            'model_name' => 'User',
                            'request' => json_encode(['dealer_id' => $dealerId, 'api' => $row]),
                            'response' => json_encode([
                                'status' => 'updated',
                                'previous' => $previous,
                                'new' => $new,
                                'api_context' => [
                                    'branch_code' => $branchCode,
                                    'branch_name' => $branchName,
                                    'zone' => $zone,
                                ],

                            ]),
                        ]);

                        DB::commit();

                        // Unset Previous Email From Existing Emails Cache
                        if (!empty($oldEmail) && $oldEmail !== $user->email) {
                            unset($existingEmails[$oldEmail]);
                        }

                        // Unset Previous Phone From Existing Phones Cache
                        if (!empty($oldPhone) && $oldPhone !== $user->phone) {
                            unset($existingPhones[$oldPhone]);
                        }

                        // Set New Email In Existing Emails Cache
                        if (!empty($user->email)) {
                            $existingEmails[$user->email] = $user->id;
                        }

                        // Set New Phone In Existing Phones Cache
                        if (!empty($user->phone)) {
                            $existingPhones[$user->phone] = $user->id;
                        }

                        $updatedCount++;





                    } catch (\Throwable $e) {

                        $errorCount++;
                        DB::rollBack();

                        LaravelLog::error('Dealer Update Failed', [
                            'dealer_id' => $dealerId,
                            'user_id' => $user->id,
                            'message' => $e->getMessage(),
                            'line' => $e->getLine(),
                            'file' => $e->getFile(),
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Last Page Check
                |--------------------------------------------------------------------------
                */
                if (count($data) < $limit) {
                    break;
                }

                $page++;
            }

            /*
            |--------------------------------------------------------------------------
            | Summary SyncLog
            |--------------------------------------------------------------------------
            */
            SyncLog::create([
                'user_id' => 0,
                'action' => 'Dealer Sync Summary',
                'model_name' => 'User',
                'request' => json_encode([
                    'pages_processed' => $pagesProcessed,
                    'total_fetched' => $totalFetched,
                    'created' => $createdCount,
                    'updated' => $updatedCount,
                    'duplicate_phone' => [
                        'count' => $duplicatePhoneCount,
                        'dealer_ids' => $duplicatePhoneDealerIds,
                    ],

                    'duplicate_email' => [
                        'count' => $duplicateEmailCount,
                        'dealer_ids' => $duplicateEmailDealerIds,
                    ],

                    'branch_not_found' => [
                        'count' => $branchNotFoundCount,
                        'dealer_ids' => $branchNotFoundDealerIds,
                    ],

                    'linked_dealer_not_found' => [
                        'count' => $linkedDealerNotFoundCount,
                        'dealer_ids' => $linkedDealerNotFoundDealerIds,
                    ],
                    'errors' => $errorCount,
                ]),
                'response' => json_encode([
                    'status' => true,
                    'has_errors' => $errorCount > 0,
                ]),
            ]);

            return response()->json([
                'status' => true,
                'pages_processed' => $pagesProcessed,
                'total_fetched' => $totalFetched,
                'created' => $createdCount,
                'updated' => $updatedCount,
                'duplicate_phone' => [
                    'count' => $duplicatePhoneCount,
                    'dealer_ids' => $duplicatePhoneDealerIds,
                ],

                'duplicate_email' => [
                    'count' => $duplicateEmailCount,
                    'dealer_ids' => $duplicateEmailDealerIds,
                ],

                'branch_not_found' => [
                    'count' => $branchNotFoundCount,
                    'dealer_ids' => $branchNotFoundDealerIds,
                ],

                'linked_dealer_not_found' => [
                    'count' => $linkedDealerNotFoundCount,
                    'dealer_ids' => $linkedDealerNotFoundDealerIds,
                ],
                'errors' => $errorCount,
            ]);

        } finally {

            /*
            |--------------------------------------------------------------------------
            | Release Lock
            |--------------------------------------------------------------------------
            */
            optional($lock)->release();
        }
    }
}
