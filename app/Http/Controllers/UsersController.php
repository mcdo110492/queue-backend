<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\User;

class UsersController extends Controller
{
    const CREATE_STATUS_MESSAGE = "User Created";
    const UPDATE_STATUS_MESSAGE = "User Updated";

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function getUsers()
    {
        $get = User::where('role','!=', 1)->get();

        return response()->json(['payload' => ['data' => $get]]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'username' => 'required|regex:/^\S*$/u|max:50|min:2|unique:users,username', //The regex rule check to ensure that there are no spaces in the username
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:2',
            'name' => 'required|max:50|min:1',
            'role' => 'required|integer'
        ]);

        $role = $request->input('role');

        if($role === 1)
        {
            return response()->json(['payload' => "Unauthorized Role Assignment"], 401);
        }

        $data = [
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
            'email' => $request->input('email'),
            'name' => $request->input('name'),
            'role' => $request->input('role')
        ];

        User::create($data);

        return response()->json(['payload' => self::CREATE_STATUS_MESSAGE], 201);

    }

    public function checkUniqueValue(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $id = $request->input('id');

        $table = 'users';

        $validate = $this->validateUniqueValue($table, $field, $value, $id);

        return response()->json($validate, $validate['status']);
    }

    public function resetPassword(Request $request)
    {

        $request->validate([
            'username' => 'required'
        ]);

        $where = ['username' => $request->input('username')];

        $count = User::where($where)->count();

        if($count === 0)
        {
            return response()->json(['payload' => "User not found"], 404);
        }

        $rawNewPassword = str_random(7);
        $newPassword = Hash::make($rawNewPassword);
        

        User::where($where)->update(['password' => $newPassword]);

        return response()->json(['payload' => ['message' => 'Password reset successfully', 'newPassword' => $rawNewPassword]], 200);
    }
}