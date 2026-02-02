<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reward;
use App\Models\UserCatalogueRedeemtion;
use App\Models\Support;
use App\Models\RejectedRedeemtion;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\GoogleTranslateService;
use App\Utils\LocalLanguageTranslation;

class DashboardController extends Controller
{

    protected $googleTranslate;
    protected $localLanguageTranslate;

    public function __construct(GoogleTranslateService $googleTranslate, LocalLanguageTranslation $localLanguageTranslate)
    {
        $this->googleTranslate = $googleTranslate;
        $this->localLanguageTranslate = $localLanguageTranslate;
    }

    function dashboardTE(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->role != 1) {
                return response()->json(['status' => false, 'msg' => 'Only TE have this permissions', 'data' => []]);
            }
            $from_date = "1990-01-31";
            $to_date = date('Y-m-d');
            if ($request->has('from_date')) {
                if (!empty($request->from_date)) {
                    $from_date = $request->from_date;
                }
            }
            if ($request->has('to_date')) {
                if (!empty($request->to_date)) {
                    $to_date = $request->to_date;
                }
            }
            $totalLinkedMason = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->count();
            $activeMason = User::where(['role' => 2, 'parent' => $user->id, 'status' => 1])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->count();
            $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->pluck('id')->toArray();
            $verifiedLifting = Reward::whereIn('user_id', $masonIds)->where(['is_verified' => 1, 'is_bonus' => 0])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->count('lifting_id');
            $unverifiedLifting = Reward::whereIn('user_id', $masonIds)->where(['is_verified' => 0, 'is_bonus' => 0])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->count();
            $totalPPCLiftingBags = Reward::with('lifting')->whereIn('user_id', $masonIds)->where(['is_bonus' => 0])->whereHas('lifting', function ($q) {
                $q->where('product_id', 1);
            })->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->sum('bag');
            $totalARCLiftingBags = Reward::with('lifting')->whereIn('user_id', $masonIds)->where(['is_bonus' => 0])->whereHas('lifting', function ($q) {
                $q->where('product_id', 2);
            })->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->sum('bag');
            // $masonNetPoints = $this->masonNetPoint($user, $from_date, $to_date);
            $masonNetPoints = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->sum('points');
            $giftRedeemed = UserCatalogueRedeemtion::whereIn('user_id', $masonIds)->whereNotNull('catalogue_id')->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->count();
            $giftPending = UserCatalogueRedeemtion::whereIn('user_id', $masonIds)->whereNotNull('catalogue_id')->where('status', 0)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->count();
            $giftDelivered = UserCatalogueRedeemtion::whereIn('user_id', $masonIds)->whereNotNull('catalogue_id')->where('status', 1)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->count();
            $giftRejected = UserCatalogueRedeemtion::whereIn('user_id', $masonIds)->whereNotNull('catalogue_id')->where('status', 2)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->count();
            $giftOrderPlaced = UserCatalogueRedeemtion::whereIn('user_id', $masonIds)->whereNotNull('catalogue_id')->where('status', 3)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->count();
            $queryRaised = Support::with('order')->whereHas('order', function ($q) use ($masonIds) {
                $q->whereIn('user_id', $masonIds);
            })->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->count();
            $queryPending = Support::with('order')->whereHas('order', function ($q) use ($masonIds) {
                $q->whereIn('user_id', $masonIds);
            })->where('status', 1)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->count();
            $queryResolved = Support::with('order')->whereHas('order', function ($q) use ($masonIds) {
                $q->whereIn('user_id', $masonIds);
            })->where('status', 2)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->count();
            return response()->json(['status' => true, 'msg' => 'Dashboard Data get successfully.', 'data' => [
                'total_linked_mason' => $totalLinkedMason,
                'active_mason' => $activeMason,
                'verified_lifting' => $verifiedLifting,
                'unverified_lifting' => $unverifiedLifting,
                'total_ppc_lifting_bags' => $totalPPCLiftingBags,
                'total_arc_lifting_bags' => $totalARCLiftingBags,
                'mason_net_points' => $masonNetPoints,
                'gift_redeemed' => $giftRedeemed,
                'gift_pending' => $giftPending,
                'gift_delivered' => $giftDelivered,
                'gift_rejected' => $giftRejected,
                'gift_order_placed' => $giftOrderPlaced,
                'query_raised' => $queryRaised,
                'query_pending' => $queryPending,
                'query_resolved' => $queryResolved
            ]]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }
    protected function masonNetPoint($user, $from_date, $to_date): int
    {
        $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->pluck('id')->toArray();
        $rewardPoint = Reward::selectRaw("IFNULL(SUM(point), 0) AS `point`")->whereIn('user_id', $masonIds)
            ->where('is_verified', 1)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->value('point');

        $redeemedPoint = UserCatalogueRedeemtion::selectRaw("IFNULL(SUM(redeemed_point), 0) AS `redeemed_point`")
            ->whereIn('user_id', $masonIds)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->value('redeemed_point');

        $rejectionCreditPoint = RejectedRedeemtion::selectRaw("IFNULL(SUM(point_credited), 0) AS `point_credited`")
            ->whereIn('user_id', $masonIds)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->value('point_credited');

        return $points = ($rewardPoint - $redeemedPoint + $rejectionCreditPoint);
    }

    function masonDashboard(Request $request)
    {
        try {
            $targetLanguage = null;
            if ($request->has("preferred_app_lang") && !empty($request->preferred_app_lang)) {
                $targetLanguage = $request->preferred_app_lang;
            }
            if (\Auth::check() && !empty(\Auth::user()->preferred_app_lang)) {
                $targetLanguage = \Auth::user()->preferred_app_lang;
            }
            $user = Auth::user();
            if ($user->role != 1) {
                return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Only_TE_have_this_permissions', $targetLanguage), 'data' => []]);
            }
            $from_date = "1990-01-31";
            $to_date = date('Y-m-d');
            if ($request->has('from_date')) {
                if (!empty($request->from_date)) {
                    $from_date = $request->from_date;
                }
            }
            if ($request->has('to_date')) {
                if (!empty($request->to_date)) {
                    $to_date = $request->to_date;
                }
            }
            $totalLinkedMasonQuery = null;
            $totalLinkedMasons = [];
            if ($request->has('status')) {
                $totalLinkedMasonQuery = User::with('branch')->where(['role' => 2, 'parent' => $user->id, 'status' => 1])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->orderBy('id', 'DESC');
            } else {
                $totalLinkedMasonQuery = User::with('branch')->where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->orderBy('id', 'DESC');
            }
            //Pagination
            if (!empty($totalLinkedMasonQuery)) {
                $page = 1;
                if ($request->has("page") && $request->page != null) {
                    $page = $request->page;
                }
                if ($page < 1) {
                    return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []]);
                }
                $dataCount = $totalLinkedMasonQuery->count();
                $limit = 6;
                $totalPage = ceil($dataCount / $limit);
                if ($page > $totalPage) {
                    return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('No_data_found', $targetLanguage), 'data' => []]);
                }
                $fetchDataFrom = $limit * ($page - 1);
                $totalLinkedMasons = $totalLinkedMasonQuery->skip($fetchDataFrom)->take($limit)->get();
            }
            //end of pagination
            $data = [];
            foreach ($totalLinkedMasons as $totalLinkedMason) {
                $dealerCodes = "";
                $i = 0;
                foreach ($totalLinkedMason->mason_dealers as $val) {
                    if ($i != 0) {
                        $dealerCodes .= ", ";
                    }
                    $dealerCodes .= $val->dealer->emp_code ?? "";
                    $i++;
                }
                $dealers = "";
                $i = 0;
                foreach ($totalLinkedMason->mason_dealers as $val) {
                    if ($i != 0) {
                        $dealers .= ", ";
                    }
                    $dealers .= $val->dealer->name ?? "";
                    $i++;
                }
                $data[] = [
                    // 'mason_name' => $this->googleTranslate->translateText($totalLinkedMason->name, $targetLanguage),
                    'mason_name' => $totalLinkedMason->name,
                    'image' => $totalLinkedMason->profile_pic == null ? "" : $user->profile_pic,
                    'address1' => $this->googleTranslate->translateText($totalLinkedMason->address1, $targetLanguage),
                    'address2' => $this->googleTranslate->translateText($totalLinkedMason->address2, $targetLanguage),
                    'city' => $this->googleTranslate->translateText($totalLinkedMason->city, $targetLanguage),
                    'district' => $this->googleTranslate->translateText($totalLinkedMason->district, $targetLanguage),
                    'state' => $this->googleTranslate->translateText($totalLinkedMason->state, $targetLanguage),
                    'country' => $this->googleTranslate->translateText($totalLinkedMason->country, $targetLanguage),
                    'pincode' => $totalLinkedMason->pincode,
                    'aadhaar_no' => $totalLinkedMason->aadhaar_no,
                    'aadhaar_doc' => $totalLinkedMason->aadhaar_doc == null ? "" : url('/public/aadhaar/') . $totalLinkedMason->aadhaar_doc,
                    'dob' => $totalLinkedMason->dob,
                    'phone' => $totalLinkedMason->phone,
                    'marital_status' => $this->localLanguageTranslate->translate($totalLinkedMason->marital_status == 1 ? "Married" : "Unmarried", $targetLanguage),
                    // 'spouse_name' => $this->googleTranslate->translateText($totalLinkedMason->spouse_name, $targetLanguage),
                    'spouse_name' => $totalLinkedMason->spouse_name,
                    'spouse_dob' => $totalLinkedMason->spouse_dob,
                    // 'branch_name' => $this->googleTranslate->translateText($totalLinkedMason->branch->name ?? "", $targetLanguage),
                    'branch_name' => $totalLinkedMason->branch->name ?? "",
                    'zone_name' => $this->googleTranslate->translateText($totalLinkedMason->branch->zone->name ?? "", $targetLanguage),
                    // 'created_by' => $this->googleTranslate->translateText($totalLinkedMason->by_created->name ?? "", $targetLanguage),
                    'created_by' => $totalLinkedMason->by_created->name ?? "",
                    // 'linked_te_name' => $this->googleTranslate->translateText($totalLinkedMason->te_linked->name ?? "", $targetLanguage),
                    'linked_te_name' => $totalLinkedMason->te_linked->name ?? "",
                    'linked_te_image' => $totalLinkedMason->te_linked->profile_pic ?? "",
                    'points' => $totalLinkedMason->points,
                    'mason_status' => $this->googleTranslate->translateText($totalLinkedMason->status, $targetLanguage),
                    'login_status' => $this->googleTranslate->translateText($totalLinkedMason->login_status, $targetLanguage),
                    'login_device_type' => $this->googleTranslate->translateText($totalLinkedMason->login_device_type, $targetLanguage),
                    // 'login_device_name' => $this->googleTranslate->translateText($totalLinkedMason->login_device_name, $targetLanguage),
                    'login_device_name' => $totalLinkedMason->login_device_name,
                    'app_version' => $totalLinkedMason->app_version,
                    'linked_dealer_code' => $dealerCodes,
                    // 'linked_dealer_name' => $this->googleTranslate->translateText($dealers, $targetLanguage),
                    'linked_dealer_name' => $dealers,
                    'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $totalLinkedMason->created_at),
                ];
            }
            return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Masons_data_successfully_fetched', $targetLanguage), 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }

    function liftingDashboard(Request $request)
    {
        try {
            $targetLanguage = null;
            if ($request->has("preferred_app_lang") && !empty($request->preferred_app_lang)) {
                $targetLanguage = $request->preferred_app_lang;
            }
            if (\Auth::check() && !empty(\Auth::user()->preferred_app_lang)) {
                $targetLanguage = \Auth::user()->preferred_app_lang;
            }
            $user = Auth::user();
            if ($user->role != 1) {
                return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Only_TE_have_this_permissions', $targetLanguage), 'data' => []]);
            }
            if (!$request->has('is_verified')) {
                return response()->json(['status' => false, 'msg' => 'is_verified params not found', 'data' => []]);
            }
            $from_date = "1990-01-31";
            $to_date = date('Y-m-d');
            if ($request->has('from_date')) {
                if (!empty($request->from_date)) {
                    $from_date = $request->from_date;
                }
            }
            if ($request->has('to_date')) {
                if (!empty($request->to_date)) {
                    $to_date = $request->to_date;
                }
            }
            $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->pluck('id')->toArray();
            $rewardQuery = null;
            if ($request->is_verified == 1) {
                $rewardQuery = Reward::with(['lifting', 'mason', 'user'])->whereIn('user_id', $masonIds)->where(['is_verified' => 1, 'is_bonus' => 0])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date));
            } else {
                $rewardQuery = Reward::with(['lifting', 'mason', 'user'])->whereIn('user_id', $masonIds)->where(['is_verified' => 0, 'is_bonus' => 0])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date));
            }
            $rewards = [];
            //Pagination
            if (!empty($rewardQuery)) {
                $page = 1;
                if ($request->has("page") && $request->page != null) {
                    $page = $request->page;
                }
                if ($page < 1) {
                    return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []]);
                }
                $dataCount = $rewardQuery->count();
                $limit = 6;
                $totalPage = ceil($dataCount / $limit);
                if ($page > $totalPage) {
                    return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('No_data_found', $targetLanguage), 'data' => []]);
                }
                $fetchDataFrom = $limit * ($page - 1);
                $rewards = $rewardQuery->skip($fetchDataFrom)->take($limit)->get();
            }
            //end of pagination
            $data = [];

            foreach ($rewards as $reward) {
                $point = Reward::where('lifting_id', $reward->lifting_id)->sum('point');
                $data[] = [
                    'date' => $reward->lifting->lifting_date ?? "",
                    // 'dealer' => $this->googleTranslate->translateText($reward->lifting->user->name ?? "", $targetLanguage),
                    'dealer' => $reward->lifting->user->name ?? "",
                    'dealer_code' => $reward->lifting->user->emp_code ?? "",
                    // 'mason_name' => $this->googleTranslate->translateText($reward->mason->name ?? "", $targetLanguage),
                    'mason_name' => $reward->mason->name ?? "",
                    'mason_mobile' => $reward->mason->phone ?? "",
                    // 'mason_branch' => $this->googleTranslate->translateText($reward->mason->branch->name ?? "", $targetLanguage),
                    'mason_branch' => $reward->mason->branch->name ?? "",
                    'te_code' => $reward->mason->te_linked->emp_code ?? "",
                    // 'te_name' => $this->googleTranslate->translateText($reward->mason->te_linked->name ?? "", $targetLanguage),
                    'te_name' => $reward->mason->te_linked->name ?? "",
                    'te_phone' => $reward->mason->te_linked->phone ?? "",
                    'zone' => $this->googleTranslate->translateText($reward->mason->branch->zone->name ?? "", $targetLanguage),
                    // 'product_name' => $this->googleTranslate->translateText($reward->lifting->product->name ?? "", $targetLanguage),
                    'product_name' => $reward->lifting->product->name ?? "",
                    'product_quantity' => $reward->lifting->qty ?? "",
                    'point' => $point,
                    'attachment' => $reward->attachment == null ? "" : url('/web/public/') . $reward->attachment,
                    'status' => $this->localLanguageTranslate->translate($reward->is_verified == 1 ? "Verified" : "Unverified", $targetLanguage),
                    // 'verified_by' => $this->googleTranslate->translateText($reward->user->name ?? "", $targetLanguage),
                    'verified_by' => $reward->user->name ?? "",
                ];
            }

            return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Data_of_lifting_got_successfully', $targetLanguage), 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }

    function liftingBagDashboard(Request $request)
    {
        try {
            $targetLanguage = null;
            if ($request->has("preferred_app_lang") && !empty($request->preferred_app_lang)) {
                $targetLanguage = $request->preferred_app_lang;
            }
            if (\Auth::check() && !empty(\Auth::user()->preferred_app_lang)) {
                $targetLanguage = \Auth::user()->preferred_app_lang;
            }
            $user = Auth::user();
            if ($user->role != 1) {
                return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Only_TE_have_this_permissions', $targetLanguage), 'data' => []]);
            }
            if (!$request->has('product_id')) {
                return response()->json(['status' => false, 'msg' => 'product_id params not found', 'data' => []]);
            }
            $from_date = "1990-01-31";
            $to_date = date('Y-m-d');
            if ($request->has('from_date')) {
                if (!empty($request->from_date)) {
                    $from_date = $request->from_date;
                }
            }
            if ($request->has('to_date')) {
                if (!empty($request->to_date)) {
                    $to_date = $request->to_date;
                }
            }

            $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->pluck('id')->toArray();
            $rewards = [];
            $rewardsQuery = null;
            if ($request->product_id == 1) {
                $rewardsQuery = Reward::with('lifting')->whereIn('user_id', $masonIds)->where(['is_bonus' => 0])->whereHas('lifting', function ($q) {
                    $q->where('product_id', 1);
                })->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date));
            }
            if ($request->product_id == 2) {
                $rewardsQuery = Reward::with('lifting')->whereIn('user_id', $masonIds)->where(['is_bonus' => 0])->whereHas('lifting', function ($q) {
                    $q->where('product_id', 2);
                })->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date));
            }

            //Pagination
            if (!empty($rewardsQuery)) {
                $page = 1;
                if ($request->has("page") && $request->page != null) {
                    $page = $request->page;
                }
                if ($page < 1) {
                    return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []]);
                }
                $dataCount = $rewardsQuery->count();
                $limit = 6;
                $totalPage = ceil($dataCount / $limit);
                if ($page > $totalPage) {
                    return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('No_data_found', $targetLanguage), 'data' => []]);
                }
                $fetchDataFrom = $limit * ($page - 1);
                $rewards = $rewardsQuery->skip($fetchDataFrom)->take($limit)->get();
            }
            //end of pagination

            $data = [];

            foreach ($rewards as $reward) {
                $point = Reward::where('lifting_id', $reward->lifting_id)->sum('point');
                $data[] = [
                    'date' => $reward->lifting->lifting_date ?? "",
                    // 'dealer' => $this->googleTranslate->translateText($reward->lifting->user->name ?? "", $targetLanguage),
                    'dealer' => $reward->lifting->user->name ?? "",
                    'dealer_code' => $reward->lifting->user->emp_code ?? "",
                    // 'mason_name' => $this->googleTranslate->translateText($reward->mason->name ?? "", $targetLanguage),
                    'mason_name' => $reward->mason->name ?? "",
                    'mason_mobile' => $reward->mason->phone ?? "",
                    // 'mason_branch' => $this->googleTranslate->translateText($reward->mason->branch->name ?? "", $targetLanguage),
                    'mason_branch' => $reward->mason->branch->name ?? "",
                    'te_code' => $reward->mason->te_linked->emp_code ?? "",
                    // 'te_name' => $this->googleTranslate->translateText($reward->mason->te_linked->name ?? "", $targetLanguage),
                    'te_name' => $reward->mason->te_linked->name ?? "",
                    'te_phone' => $reward->mason->te_linked->phone ?? "",
                    'zone' => $this->googleTranslate->translateText($reward->mason->branch->zone->name ?? "", $targetLanguage),
                    // 'product_name' => $this->googleTranslate->translateText($reward->lifting->product->name ?? "", $targetLanguage),
                    'product_name' => $reward->lifting->product->name ?? "",
                    'product_quantity' => $reward->lifting->qty ?? "",
                    'point' => $point,
                    'attachment' => $reward->attachment == null ? "" : url('/web/public/') . $reward->attachment,
                    'status' => $this->localLanguageTranslate->translate($reward->is_verified == 1 ? "Verified" : "Unverified", $targetLanguage),
                    // 'verified_by' => $this->googleTranslate->translateText($reward->user->name ?? "", $targetLanguage),
                    'verified_by' => $reward->user->name ?? "",
                ];
            }

            return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Data_of_lifting_bags_got_successfully', $targetLanguage), 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }

    function masonNetPointDashboard(Request $request)
    {
        try {
            $targetLanguage = null;
            if ($request->has("preferred_app_lang") && !empty($request->preferred_app_lang)) {
                $targetLanguage = $request->preferred_app_lang;
            }
            if (\Auth::check() && !empty(\Auth::user()->preferred_app_lang)) {
                $targetLanguage = \Auth::user()->preferred_app_lang;
            }
            $user = Auth::user();
            if ($user->role != 1) {
                return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Only_TE_have_this_permissions', $targetLanguage), 'data' => []]);
            }
            $from_date = "1990-01-31";
            $to_date = date('Y-m-d');
            if ($request->has('from_date')) {
                if (!empty($request->from_date)) {
                    $from_date = $request->from_date;
                }
            }
            if ($request->has('to_date')) {
                if (!empty($request->to_date)) {
                    $to_date = $request->to_date;
                }
            }
            $masonIds = [];
            $masonIdQuery = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date));

            //Pagination
            if (!empty($masonIdQuery)) {
                $page = 1;
                if ($request->has("page") && $request->page != null) {
                    $page = $request->page;
                }
                if ($page < 1) {
                    return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []]);
                }
                $dataCount = $masonIdQuery->count();
                $limit = 6;
                $totalPage = ceil($dataCount / $limit);
                if ($page > $totalPage) {
                    return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('No_data_found', $targetLanguage), 'data' => []]);
                }
                $fetchDataFrom = $limit * ($page - 1);
                $masonIds = $masonIdQuery->skip($fetchDataFrom)->take($limit)->get();
            }
            //end of pagination

            $data = [];

            foreach ($masonIds as $masonId) {
                $data[] = [
                    // 'mason_name' => $this->googleTranslate->translateText($masonId->name, $targetLanguage),
                    'mason_name' => $masonId->name,
                    'contact' => $masonId->phone,
                    'points' => $masonId->points,
                    // 'mason_category' => $this->googleTranslate->translateText($masonId->mason_category->name ?? "", $targetLanguage),
                    'mason_category' => $masonId->mason_category->name ?? "",
                    // 'branch_name' => $this->googleTranslate->translateText($masonId->branch->name ?? "", $targetLanguage),
                    'branch_name' => $masonId->branch->name ?? "",
                    'zone' => $this->googleTranslate->translateText($masonId->branch->zone->name ?? "", $targetLanguage),
                    'te_code' => $masonId->te_linked->emp_code ?? "",
                    // 'te_name' => $this->googleTranslate->translateText($masonId->te_linked->name ?? "", $targetLanguage),
                    'te_name' => $masonId->te_linked->name ?? "",
                    'te_mobile' => $masonId->te_linked->phone ?? "",
                ];
            }

            return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Data_of_mason_points_got_successfully', $targetLanguage), 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }

    function giftDashboard(Request $request)
    {
        try {
            $targetLanguage = null;
            if ($request->has("preferred_app_lang") && !empty($request->preferred_app_lang)) {
                $targetLanguage = $request->preferred_app_lang;
            }
            if (\Auth::check() && !empty(\Auth::user()->preferred_app_lang)) {
                $targetLanguage = \Auth::user()->preferred_app_lang;
            }
            $user = Auth::user();
            if ($user->role != 1) {
                return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Only_TE_have_this_permissions', $targetLanguage), 'data' => []]);
            }
            $from_date = "1990-01-31";
            $to_date = date('Y-m-d');
            if ($request->has('from_date')) {
                if (!empty($request->from_date)) {
                    $from_date = $request->from_date;
                }
            }
            if ($request->has('to_date')) {
                if (!empty($request->to_date)) {
                    $to_date = $request->to_date;
                }
            }
            $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->pluck('id')->toArray();
            $giftStatuQuery = null;
            $giftStatus = [];
            if ($request->has('status')) {
                if ($request->status == 1) {
                    $giftStatuQuery = UserCatalogueRedeemtion::whereIn('user_id', $masonIds)->whereNotNull('catalogue_id')->where('status', 1)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date));
                } else if ($request->status == 2) {
                    $giftStatuQuery = UserCatalogueRedeemtion::whereIn('user_id', $masonIds)->whereNotNull('catalogue_id')->where('status', 2)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date));
                } else if ($request->status == 3) {
                    $giftStatuQuery = UserCatalogueRedeemtion::whereIn('user_id', $masonIds)->whereNotNull('catalogue_id')->where('status', 3)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date));
                } else {
                    $giftStatuQuery = UserCatalogueRedeemtion::whereIn('user_id', $masonIds)->whereNotNull('catalogue_id')->where('status', 0)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date));
                }
            } else {
                $giftStatuQuery = UserCatalogueRedeemtion::whereIn('user_id', $masonIds)->whereNotNull('catalogue_id')->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date));
            }

            //Pagination
            if (!empty($giftStatuQuery)) {
                $page = 1;
                if ($request->has("page") && $request->page != null) {
                    $page = $request->page;
                }
                if ($page < 1) {
                    return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []]);
                }
                $dataCount = $giftStatuQuery->count();
                $limit = 6;
                $totalPage = ceil($dataCount / $limit);
                if ($page > $totalPage) {
                    return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('No_data_found', $targetLanguage), 'data' => []]);
                }
                $fetchDataFrom = $limit * ($page - 1);
                $giftStatus = $giftStatuQuery->skip($fetchDataFrom)->take($limit)->get();
            }
            //end of pagination

            $data = [];
            $status = ['0' => 'Pending', '1' => 'Delivered', '2' => 'Rejected', '3' => 'Order Placed'];

            foreach ($giftStatus as $gift) {
                $data[] = [
                    'date' => \Carbon\Carbon::parse($gift->created_at)->format("Y-m-d"),
                    'order_no' => $gift->order_id,
                    // 'mason_name' => $this->googleTranslate->translateText($gift->user->name ?? "", $targetLanguage),
                    'mason_name' => $gift->user->name ?? "",
                    'mason_phone' => $gift->user->phone ?? "",
                    // 'employee_name' => $this->googleTranslate->translateText($gift->user->te_linked->name ?? "", $targetLanguage),
                    'employee_name' => $gift->user->te_linked->name ?? "",
                    'employee_id' => $gift->user->te_linked->emp_code ?? "",
                    // 'branch' => $this->googleTranslate->translateText($gift->user->branch->name ?? "", $targetLanguage),
                    'branch' => $gift->user->branch->name ?? "",
                    'address1' => $this->googleTranslate->translateText($gift->user->address1 ?? "", $targetLanguage),
                    'address2' => $this->googleTranslate->translateText($gift->user->address2 ?? "", $targetLanguage),
                    'city' => $this->googleTranslate->translateText($gift->user->city ?? "", $targetLanguage),
                    'district' => $this->googleTranslate->translateText($gift->user->district ?? "", $targetLanguage),
                    'state' => $this->googleTranslate->translateText($gift->user->state ?? "", $targetLanguage),
                    'country' => $this->googleTranslate->translateText($gift->user->country ?? "", $targetLanguage),
                    'pincode' => $this->googleTranslate->translateText($gift->user->pincode ?? "", $targetLanguage),
                    // 'catalogue' => $this->googleTranslate->translateText($gift->catalogue->name ?? "", $targetLanguage),
                    'catalogue' => $gift->catalogue->name ?? "",
                    'status' => $this->localLanguageTranslate->translate($status[$gift->status] ?? "", $targetLanguage),
                    'updated_at' => \Carbon\Carbon::parse($gift->updated_at)->format("Y-m-d"),
                    'delivery_date' => $gift->delivery_date,
                ];
            }

            return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Data_of_gift_status_got_successfully', $targetLanguage), 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }

    function queryDashboard(Request $request)
    {
        try {
            $targetLanguage = null;
            if ($request->has("preferred_app_lang") && !empty($request->preferred_app_lang)) {
                $targetLanguage = $request->preferred_app_lang;
            }
            if (\Auth::check() && !empty(\Auth::user()->preferred_app_lang)) {
                $targetLanguage = \Auth::user()->preferred_app_lang;
            }
            $user = Auth::user();
            if ($user->role != 1) {
                return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('Only_TE_have_this_permissions', $targetLanguage), 'data' => []]);
            }
            $from_date = "1990-01-31";
            $to_date = date('Y-m-d');
            if ($request->has('from_date')) {
                if (!empty($request->from_date)) {
                    $from_date = $request->from_date;
                }
            }
            if ($request->has('to_date')) {
                if (!empty($request->to_date)) {
                    $to_date = $request->to_date;
                }
            }
            $masonIds = User::where(['role' => 2, 'parent' => $user->id])->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date))->pluck('id')->toArray();
            $queries = [];
            $rawQueries = null;
            if ($request->has('status')) {
                if ($request->status == 1) {
                    $rawQueries = Support::with('order')->whereHas('order', function ($q) use ($masonIds) {
                        $q->whereIn('user_id', $masonIds);
                    })->where('status', 1)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date));
                } else if ($request->status == 2) {
                    $rawQueries = Support::with('order')->whereHas('order', function ($q) use ($masonIds) {
                        $q->whereIn('user_id', $masonIds);
                    })->where('status', 2)->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date));
                }
            } else {
                $rawQueries = Support::with('order')->whereHas('order', function ($q) use ($masonIds) {
                    $q->whereIn('user_id', $masonIds);
                })->whereBetween(DB::raw('DATE(created_at)'), array($from_date, $to_date));
            }
            //Pagination
            if (!empty($rawQueries)) {
                $page = 1;
                if ($request->has("page") && $request->page != null) {
                    $page = $request->page;
                }
                if ($page < 1) {
                    return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []]);
                }
                $dataCount = $rawQueries->count();
                $limit = 6;
                $totalPage = ceil($dataCount / $limit);
                if ($page > $totalPage) {
                    return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('No_data_found', $targetLanguage), 'data' => []]);
                }
                $fetchDataFrom = $limit * ($page - 1);
                $queries = $rawQueries->skip($fetchDataFrom)->take($limit)->get();
            }
            //end of pagination
            $data = [];
            $status = ['0' => 'Pending', '1' => 'Delivered', '2' => 'Rejected', '3' => 'Order Placed'];
            $supportType = ['1' => 'Not_delivered', '2' => 'Defective', '3' => 'Delivered'];
            foreach ($queries as $query) {
                $data[] = [
                    'order_no' => $query->order->order_id,
                    // 'mason_name' => $this->googleTranslate->translateText($query->order->user->name, $targetLanguage),
                    'mason_name' => $query->order->user->name,
                    'mason_phone' => $query->order->user->phone,
                    // 'employee_name' => $this->googleTranslate->translateText($query->order->user->te_linked->name, $targetLanguage),
                    'employee_name' => $query->order->user->te_linked->name,
                    'employee_id' => $query->order->user->te_linked->emp_code,
                    // 'branch' => $this->googleTranslate->translateText($query->order->user->branch->name, $targetLanguage),
                    'branch' => $query->order->user->branch->name,
                    'type' => $this->localLanguageTranslate->translate($supportType[$query->support_type] ?? "", $targetLanguage),
                    'comment' => $this->googleTranslate->translateText($query->comment, $targetLanguage),
                    'status' => $this->localLanguageTranslate->translate($status[$query->status] ?? "", $targetLanguage),
                    'updated_at' => \Carbon\Carbon::parse($query->updated_at)->format("Y-m-d"),
                ];
            }

            return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Data_of_query_status_got_successfully', $targetLanguage), 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'data' => []]);
        }
    }


    public function getMasonList(Request $request)
    {
        try {
            $empcode = $request->query('emp_code'); 
            $perPage = $request->query('per_page', 10);

            if (empty($empcode)) {
                return response()->json([
                    'status' => false,
                    'msg'    => 'empcode parameter is required',
                    'data'   => [],
                ]);
            }

            $users = User::select('id', 'name', 'phone')
                ->where(['role'=> 2,'status'=>1])
                ->whereHas('te_linked', function ($query) use ($empcode) {
                    $query->where('emp_code', $empcode);
                })->get();
                //->paginate($perPage);

            return response()->json([
                'status' => true,
                'msg'    => 'Data fetched successfully',
                'data'   => $users,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTraceAsString(),
                'data'   => [],
            ]);
        }
    }
}
