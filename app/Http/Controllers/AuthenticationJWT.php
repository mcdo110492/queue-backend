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

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['payload' => "Logout Successfully"],200);
    }


    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function respondWithToken($token)
    {
        $payload = ['access_token'=> $token, 'token_type' => 'bearer'];
        return response()->json(compact('payload'),200);
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