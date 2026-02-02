<?php

namespace App\Http\Controllers\StarSathi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        $user = \Auth::user();
        $page = 1;
        if($request->has("page") && $request->page != null)
        {
            $page = $request->page;
        }
        if($page < 1)
        {
            return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []]);
        }
        $dataCount = Notification::where("notifiable_id", $user->id)->count();
        $limit = 10;
        $totalPage = ceil($dataCount / $limit);
        if($page > $totalPage)
        {
            return response()->json(['status' => false, 'msg' => 'No Notifications.', 'data' => []]);
        }
        $fetchDataFrom = $limit * ($page - 1);
        $notifications = Notification::where("notifiable_id", $user->id)->skip($fetchDataFrom)->take($limit)->orderBy("created_at", "DESC")->get();
        $msg = [];
        try
        {
            \DB::beginTransaction();
                foreach($notifications as $notification)
                {
                    array_push($msg, ["msg" => json_decode($notification->data)->msg]);
                    if($notification->read_at == null)
                    {
                        $notification->update(['read_at' => now()]);
                    }
                }
            \DB::commit();
        }
        catch(\Exception $e)
        {
            \DB::rollBack();
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }
        $see_more = true;
        if($page == $totalPage)
        {
            $see_more = false;
        }
        $unread_msg_count = Notification::where(["notifiable_id" => $user->id, "read_at" => null])->count();
        return response()->json(['status' => true, 'msg' => 'Notifications Get Successfully.', 'data' => [
            "notifications" => $msg,
            "see_more" => $see_more,
            "unread_msg_count" => $unread_msg_count,
            "current_page" => $page,
        ]]);
    }
}
