<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Services\GoogleTranslateService;
use App\Utils\LocalLanguageTranslation;

class NotificationController extends Controller
{
    protected $googleTranslate;
    protected $localLanguageTranslate;

    public function __construct(GoogleTranslateService $googleTranslate, LocalLanguageTranslation $localLanguageTranslate)
    {
        $this->googleTranslate = $googleTranslate;
        $this->localLanguageTranslate = $localLanguageTranslate;
    }

    public function getNotifications(Request $request)
    {
        $targetLanguage = null;
        if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
        {
            $targetLanguage = $request->preferred_app_lang;
        }
        if(\Auth::check() && !empty(\Auth::user()->preferred_app_lang))
        {
            $targetLanguage = \Auth::user()->preferred_app_lang;
        }
        $user = \Auth::user();
        $page = 1;
        if($request->has("page") && $request->page != null)
        {
            $page = $request->page;
        }
        if($page < 1)
        {
            return response()->json(['status' => false, 'msg' => 'Incorrect Page. Should be started from 1.', 'data' => []], 404);
        }
        $limit = 10;
        $fetchDataFrom = $limit * ($page - 1);
        $notifications = Notification::where("notifiable_id", $user->id)->skip($fetchDataFrom)->take($limit)->orderBy("created_at", "DESC")->get();
        if(empty($notifications))
        {
            return response()->json(['status' => false, 'msg' => $this->localLanguageTranslate->translate('No_notifications', $targetLanguage), 'data' => []], 404);
        }
        $msg = [];
        try
        {
            \DB::beginTransaction();
                $msgId = $fetchDataFrom;
                foreach($notifications as $notification)
                {
                    $notificationMsg = json_decode($notification->data)->msg;
                    if(!empty($notificationMsg))
                    {
                        try{
                            $notificationMsg = $this->googleTranslate->translateText($notificationMsg, $targetLanguage);
                        }
                        catch(\Exception $e)
                        {
                            \Log::error($e->getMessage());
                        }
                    }
                    array_push($msg, [
                        "msg" => $notificationMsg,
                        "id" => $msgId
                    ]);
                    $notification->update(['read_at' => now()]);
                    $msgId++;
                }
            \DB::commit();
        }
        catch(\Exception $e)
        {
            \DB::rollBack();
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 404);
        }
        return response()->json(['status' => true, 'msg' => $this->localLanguageTranslate->translate('Notification_got_successfully', $targetLanguage), 'data' => [
            "notifications" => $msg
        ]]);
    }
}
