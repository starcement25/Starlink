<?php
namespace App\Utils;

use App\Models\Page;
use App\Models\User;
use App\Models\Notification;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class Helper{

    public static function checkIsUserAuthorizeToPerformTheTask($taskName)
    {
        
        abort_if(!auth()->user()->can($taskName), 403, 'User does not have the right permissions.');
    }

    public static function getUserUnreadMsgCount()
    {
        return Notification::where(["notifiable_id" => \Auth::user()->id ?? "", "read_at" => null])->count();
    }



    public static function getAllPages()
    {
        $pages = Page::all();
        return $pages ;
    }

    public static function getElementType($type)
    {
        $element = [
                    'select' => 'Select',
                    'searchable_select' => 'Searchable Select',
                    'text' => 'Text Box', 'label'=> 'Label',
                    'checkbox'=> 'Check Box',
                    'heading'=> 'Heading',
                    'rank'=> 'Ranking'
                     ] ;
        
        return $element[$type] ?? "" ;
    }

    public static function getUserId()
    {
       return \Auth::user()->id ?? null ;
    }

    public static function isBranchExist($id)
    {
       $data = Branch::find($id);
       return !empty($data) ? true: false ;
    }

    public static function getUser($id)
    {
       return User::find($id) ;
    }
}