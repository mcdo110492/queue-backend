<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\User;

class AuthenticationJWT extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login()
    {
        $credentials = request(['username', 'password']);

        $user = User::where(['username' => $credentials['username']])->firstOrFail();

        if(!$this->checkUserStatus($user))
        {
            return response()->json(['payload' => 'Unauthorized Access'], 401);
        }

        if(! $token = auth()->claims(compact('user'))->attempt($credentials))
        {
            return response()->json(['payload' => 'Invalid username or password'], 401);
        }

        $payload = ['user' => $user, 'token' => $token];

        return response()->json(compact('payload'),200);

    }

    public function me()
    {
        $user = auth()->user();

        return response()->json(['payload' => compact('user')],200);
    }



    public function refresh()
    {

        $token = auth()->refresh();

        return response()->json(['payload' => compact('token')], 200);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['payload' => "Logout Successfully"],200);
    }

    public function checkUserStatus($user)
    {
        $status = $user->status;

        if($status == 0)
        {
            return false;
        }

        return true;
    }
    


}