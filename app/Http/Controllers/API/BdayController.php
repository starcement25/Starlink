<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BdayController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //    public function getBday($id)
    // {
    //     $user = User::where('id', $id)
    //                 ->where('role', 2)
    //                 ->first();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'User not found or role is not Mason'
    //         ], 404);
    //     }

    //     if (is_null($user->dob)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Date of birth is not available for this user'
    //         ], 200);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'dob' => $user->dob
    //     ], 200);
    // }

    public function getBday($id)
    {

        $user = User::where('id', $id)
            ->where('role', 2)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ]);
        }


        if (empty($user->dob)) {
            return response()->json([
                'status' => false,
                'message' => 'Birthday Not Found'
            ]);
        }

        $today = Carbon::today();
        $dob   = Carbon::parse($user->dob);


        if ($today->format('m-d') !== $dob->format('m-d')) {
            return response()->json([
                'status' => false,
                'message' => 'Today is not birthday'
            ]);
        }


        $alreadyShown = DB::table('birthday_wish_seen_log')
            ->where('user_id', $user->id)
            ->whereDate('seen_date', $today)
            ->where('seen_year', $today->year)
            ->exists();

        if ($alreadyShown) {
            return response()->json([
                'status' => false,
                'message' => 'Birthday already shown'
            ]);
        }


         $birthdayData = DB::table('birthday_master')->first();

// dd( $birthdayData);
        // DB::table('birthday_wish_seen_log')->insert([
        //     'customer_id' => $user->id,
        //     'seen_date'   => $today,
        //     'seen_year'   => $today->year,
        //     'created_at'  => now()
        // ]);


        return response()->json([
            "status"   => true,
            "user_code" => $user->id,
            "user_name" => $user->name,
            "type"     => $birthdayData->type ?? "Happy Birthday",
            "fromField"     => $birthdayData->fromField ?? "~ Star Cement Family",
            "title"    => $birthdayData->title ?? "Happy Birthday",
            "message"  => $birthdayData->message ?? "We wish you a wonderful year ahead. Thank you for being with us!",
            "img" => !empty($birthdayData->img) ?"https://dev.starlinkinfluencers.in/web/public/". $birthdayData->img :"",
            "dob"      => $dob->format('d-m-Y')
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeBday(Request $request)
    {
        // Validate input
        $request->validate([
            'id'  => 'required|integer|exists:users,id',
            'dob' => 'required|date'
        ]);

        $user = User::where('id', $request->id)
            ->where('role', 2)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found or role is not Mason'
            ], 404);
        }

        if ($user->dob !== null) {
            return response()->json([
                'status' => false,
                'message' => 'DOB is already updated'
            ]);
        }

        $user->dob = $request->dob;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'DOB stored successfully',
            'dob' => $user->dob
        ]);
    }





    public function checkAndLogBirthday(Request $request)
{
 
    $request->validate([
        'id' => 'required|integer|exists:users,id',
    ]);

    $user = User::where('id', $request->id)
                ->where('role', 2)
                ->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not found or role not Mason'
        ], 404);
    }

    if (empty($user->dob)) {
        return response()->json([
            'status' => false,
            'message' => 'DOB not updated'
        ]);
    }

    $today = Carbon::today();
    $dob   = Carbon::parse($user->dob);

    if ($today->format('m-d') !== $dob->format('m-d')) {
        return response()->json([
            'status' => false,
            'message' => 'Today is not birthday'
        ]);
    }

   
    $exists = DB::table('birthday_wish_seen_log')
        ->where('user_id', $user->id)
        ->whereDate('seen_date', $today)
        ->where('seen_year', $today->year)
        ->exists();

    if ($exists) {
        return response()->json([
            'status' => false,
            'message' => 'Birthday wish log already exists for today'
        ]);
    }

 
    DB::table('birthday_wish_seen_log')->insert([
        'user_id' => $user->id,
        'seen_date'   => $today,
        'seen_year'   => $today->year,
        'created_at'  => now()
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Birthday wish log inserted successfully'
    ]);
}
}
