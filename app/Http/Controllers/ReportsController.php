<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use \Tymon\JWTAuth\Exceptions\UserNotDefinedException;

use App\Tickets;
use App\TicketsUsers;
use App\UsersCounters;

class ReportsController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api', ['except' => ['generate']]);
        
    }

    public function getAuthUser() {
        $user = auth()->user();
        return $user->id;
    }

    public function getAuthUserDepartment() {
        $user = auth()->user();
        return $user->department_id;
    }

    public function getByDepartment(Request $request)
    {
        $rawDate = $request->input('date');
        $date = Carbon::parse($rawDate)->toDateString();

        $q = Tickets::with(['latestUser.user','department'])->where(['status' => $status,'date_issued' => $date])->get();

        $payload = ['data' => $q];

        return response()->json(compact('payload'), 200);

    }

    public function getByUser(Request $request){
        
        $rawDate = $request->input('date');
        $date = Carbon::parse($rawDate)->toDateString();

        $user_id = $request->input('user');

        $q = Tickets::with(['oneUser.user']);
    }
}